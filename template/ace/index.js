// runs Ace's demo or builds the HTML static files
'use strict';

const express = require('express');
const extend = require('xtend');
const path = require('path');
const fs = require('fs');

const constants = require('./utils/constants');
const resolve = require('./utils/app-resolve');
const { Page, Display, HbsHelpers } = require('./utils/hbs-helpers');

let build = process.env.BUILD;
let htmlOutput = process.env.HTML == 'true';

let isForOnlineDemo = build === 'dist';
let requireDemoAssets = [];

class App {
	
	constructor(demo=constants.DEFAULT_DEMO) {
		this.base = `./${constants.APP_FOLDER}/views/${demo}`;
		this.data = `./${constants.APP_FOLDER}/data/${demo}`;

		this.app = express();
		
		this.display = new Display(this.base, this.data);
		this.display.connect(this.app);		
	}

	runServer() {
		this.app
		.get('/', function (req, res) {
			res.redirect('/'+constants.DEFAULT_PAGE);
		})
		.get('/favicon.ico',  (req, res) => {
			res.redirect('/assets/favicon.png');
		})
		
		.get('/docs',  (req, res) => {
			res.redirect('/docs/index.html');
		})
		.get('/:page',  (req, res) => {
			let requestedPageId = req.params.page || 'page-error';
			this._renderPage(requestedPageId, res);
		})
		
		this.app.use('/node_modules', express.static(path.join(__dirname, './node_modules')));
		this.app.use('/assets', express.static(path.join(__dirname, './assets')));
		this.app.use('/dist', express.static(path.join(__dirname, './dist')));
		this.app.use('/docs', express.static(path.join(__dirname, './docs')));

		this.app.use(`/${constants.APP_FOLDER}`, express.static(path.join(__dirname, `./${constants.APP_FOLDER}`)));
		
		this.app.listen(process.env.PORT || constants.DEFAULT_PORT);
	}


	generateHTML() {
		let path = resolve.AppDirAbs()+'/data/'+constants.DEFAULT_DEMO+'/layouts/pages.json';
	
		var sidebarData = null;
		
		try {
			sidebarData = JSON.parse(fs.readFileSync(path, 'utf-8'));
		}
		catch(err) {
			//if (!(err instanceof Error) || err.code !== 'MODULE_NOT_FOUND') throw err;
			console.log("Unable to generate HTML files");
			return;
		}

		HbsHelpers.keepRequiredAssets(!isForOnlineDemo);

		for(var d in sidebarData) {
			var pageInfo = sidebarData[d];
			if(pageInfo.link != false && pageInfo.id != null) {
				this._renderPage(pageInfo.id, `${constants.HTML_FOLDER}/${pageInfo.id}.html`);
			}
		}
	}
	

	_renderPage(requestedPageId, outputStream) {
		var demo = constants.DEFAULT_DEMO;
		
		let page = new Page(requestedPageId, this.base, this.data);
		page.setLayout(this.display.getLayout());

		let layoutInfo = page.getLayoutInfo() || constants.DEFAULT_LAYOUT;//which is 'main'
		
		this.display.updatePagePartialsDirFor(page);
		
		this.app.render(page.getTemplate(), {
			useCDN: isForOnlineDemo,
			staticHTML: htmlOutput,

			layout: layoutInfo,
		
			demoName: demo,
			
			//assign the variables
			pageId: page.id,
			title: page.getTitle(),
			description: page.getDescription(),
			
			sidebarItems: page.getSidebar(),
			breadcrumbs: page.getBreadcrumbs(),
			
			appFolder: constants.APP_FOLDER,
			
			helpers: extend(
				HbsHelpers.Helpers(page),
				
				{
					getPageLink: function (uri) {
						return uri ? (htmlOutput ? `${constants.HTML_FOLDER}/${uri}.html` : uri) : '#';
					}
				}
			)
			
			}, function(err, output){
				if(err) console.log(err);
				
				if( typeof outputStream === 'string' ) {					
					fs.writeFileSync(outputStream, output);//save to file
				}

				else outputStream.send(output);//send to browser
			}
		);
	}
	

}

let app = new App();

if (htmlOutput) {
	if( !fs.existsSync(constants.HTML_FOLDER) ) fs.mkdirSync(constants.HTML_FOLDER);
	app.generateHTML();

	if (!isForOnlineDemo) {
		// save the list of required assets to be put in final zip file
		// because 'render' function is async, we do this on 'exit'
		process.on('exit', function () {
			let requireDemoAssets = HbsHelpers.getRequiredAssets();
			requireDemoAssets = [...new Set(requireDemoAssets)]; //convert to `Set` to remove duplicates
			requireDemoAssets = requireDemoAssets.filter((item) => item.match(/node_modules/))
			requireDemoAssets = requireDemoAssets.map(item => {			
				return item.replace(/^(\W)*node_modules/ , 'node_modules')

				// or include the whole folder (but package size will be too much)
				// return item.replace(/^(?:\W)*(node_modules\/(?:[^\/]+)).*$/ , '$1')
			});
			fs.writeFileSync("required-assets.txt", JSON.stringify(requireDemoAssets, null, "  "))
		});
	}
}

else {
	app.runServer();
}

