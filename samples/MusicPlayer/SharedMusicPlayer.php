<?php


/* @var $f Flexed */
if (isset ( $f )) {
	$f->package = "Models/";
	$f->company = "MusicPlayer";
	$f->prefix = "MusicPlayer";
	$f->author = "benoit@pereira-da-silva.com";
	$f->projectName = "MusicPlayer";
	$f->license = FLEXIONS_MODULES_DIR."licenses/LGPL.template.php";
}

$parentClass = "WattModel";
$collectionParentClass="WattCollectionOfModel";
$protocols="WattCoding,WattCopying,WattExtraction";
$imports = "\n#import \"$parentClass.h\"\n";
$markAsDynamic = false;
$allowScalars = true;