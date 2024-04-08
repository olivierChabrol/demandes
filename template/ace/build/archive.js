'use strict'
var path = require('path')
var fs = require('fs')
var archiver = require('archiver')

const isForOnlineDemo = process.env.PACKAGE === 'demo'

var base = process.cwd() === __dirname ? '../' : ''
const pkg = require(path.join(__dirname, '/../package.json'))

var output = fs.createWriteStream(`${base}${isForOnlineDemo ? 'demo' : 'ace'}-v${pkg.version}.zip`)

var archive = archiver('zip')

output.on('end', function () {
  console.log('Data has been drained')
})

// good practice to catch warnings (ie stat failures and other non-blocking errors)
archive.on('warning', function (err) {
  if (err.code === 'ENOENT') {
    // log warning
  } else {
    // throw error
    throw err
  }
})

// good practice to catch this error explicitly
archive.on('error', function (err) {
  throw err
})

archive.pipe(output)

var list = []

if (isForOnlineDemo) {
  list = [
    'assets/js/demo.min.js',
	  'assets/image',
	  'assets/favicon.png',
    'dist/**/**/*.min.js',
	'dist/**/**/*.min.css',
    'html'
  ]
} else {
  list = [
    'application',
    'assets',
    'build',
    'dist',
	  'html',
    'utils',

    'docs',

    'index.js',
    'package.json',
	  'package-lock.json',
    '.gitignore',
    '.env',
    '.babelrc',
	  '.browserslistrc',
	  'changelog.md',
	  'README.md' 
  ]
}

for (var item of list) {
  if (item.indexOf('*') >= 0) {
    archive.glob(`${base}${item}`)
    continue
  }
  if (!fs.existsSync(`${base}${item}`)) continue

  if (fs.lstatSync(`${base}${item}`).isDirectory()) {
    archive.directory(`${base}${item}`, item)
  } else {
    archive.file(`${base}${item}`, { name: item })
  }
}



if (!isForOnlineDemo) {
  // add documentation files
  var extra = {
    'docs/assets/style.css' : '../ace-docs/assets/style.css',
    'docs/assets/docs.js': '../ace-docs/assets/docs.js',
    'docs/images' : '../ace-docs/images'
  }

  for (var name in extra) {
    var item = extra[name];
    if (item.indexOf('*') >= 0) {
      archive.glob(`${base}${item}`)
      continue
    }
    if (!fs.existsSync(`${base}${item}`)) continue

    if (fs.lstatSync(`${base}${item}`).isDirectory()) {
      archive.directory(`${base}${item}`, name)
    } else {
      archive.file(`${base}${item}`, { name: name })
    }
  }

  // add required node_modules files
  try {
    var assetsList = fs.readFileSync('required-assets.txt')
    fs.unlinkSync('required-assets.txt')

    assetsList = JSON.parse(assetsList)

    assetsList.push('node_modules/@fortawesome/fontawesome-free/webfonts')
	assetsList.push('node_modules/jam-icons/fonts')
	assetsList.push('node_modules/eva-icons/style/fonts')

    //assets required for documentation
    assetsList.push('node_modules/bootstrap/dist/css/bootstrap-reboot.css')
    assetsList.push('node_modules/prism-themes/themes/prism-material-light.css')
    assetsList.push('node_modules/prismjs/plugins/line-highlight/prism-line-highlight.css')
    assetsList.push('node_modules/prismjs/prism.js')
    assetsList.push('node_modules/prismjs/plugins/line-highlight/prism-line-highlight.js')

    for (var item of assetsList) {
      if (!fs.existsSync(`${base}${item}`)) continue

      if (fs.lstatSync(`${base}${item}`).isFile()) {    
        archive.file(`${base}${item}`, { name: item })
      }
      else {
        archive.directory(`${base}${item}`, item)
      }  
    }
  } 
  catch(e) {
    console.log(e)
  }

  archive.append("<html><head><meta http-equiv='refresh' content='0; url=./html/dashboard.html' /></head></html>", { name: 'index.html' })

  // for documentation
  archive.append("<html><head><meta http-equiv='refresh' content='0; url=./docs/html/index.html' /></head></html>", { name: 'documentation.html' })
  archive.append("<html><head><meta http-equiv='refresh' content='0; url=./html/index.html' /></head></html>", { name: 'docs/index.html' })
}

else {
  archive.append("<html><head><meta http-equiv='refresh' content='0; url=./html/dashboard.html' /></head></html>", { name: 'index.html' })
}



archive.finalize()
