#!/bin/bash

path="upload"

mkdir -p $path/extract
mkdir -p $path/logo
mkdir -p $path/mission-order
mkdir -p $path/purchase-order
mkdir -p $path/ticket
mkdir -p $path/tmp

logosorig="logos"

cp "$logosorig/logo.png" $path/logo/.
cp "$logosorig/siged.png" $path/logo/.

echo "L'arborescence du dossier upload a bien été créée"