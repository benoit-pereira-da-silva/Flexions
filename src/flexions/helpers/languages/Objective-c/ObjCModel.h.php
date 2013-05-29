<?php

/*
 * Created by Benoit Pereira da Silva on 20/04/2013. Copyright (c) 2013
 * http://www.pereira-da-silva.com 
 * 
 * This file is part of Flexions Flexions is free software: you can
 * redistribute it and/or modify it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as
 * published by the Free Software Foundation, either version 3 of the License, or (at your option)
 * any later version. 
 * 
 * Flexions is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE. See the GNU LESSER GENERAL PUBLIC LICENSE for more details. You should have received a
 * copy of the GNU LESSER GENERAL PUBLIC LICENSE along with Flexions If not, see
 * <http://www.gnu.org/licenses/>
 */

/**
 * When using this template you must define : $f->prefix
 * and you can inject : $imports, $parentClass
 */
require_once FLEXIONS_ROOT_DIR . 'flexions/helpers/languages/Objective-c/ObjCGeneric.functions.php';

/* @var $f Flexed */
/* @var $d EntityRepresentation */
/* @var $languageHelper ObjectiveCHelper */
/* @var $imports string */
/* @var $parentClass string */

$f->fileName = getCurrentClassNameFragment ( $d, $f->prefix ) . '.h';
$languageHelper = new ObjectiveCHelper ();

?><?php 
// ////////// GENERATION STARTS HERE //////////
?>
<?php


if ($f->license != null)
	include $f->license;
?>
<?php echo getCommentHeader($f);?>
<?php echo getDocumentation($d );?>
<?php echo $imports?>
<?php

// Import the parent class.
if ($d->instanceOf != null) {
	echoindent ( "#import \"" . $d->instanceOf . ".h\"\n", 0 );
}
// /////////////////////////////////////////////////////////////////////////
// Explicit collection generation directive : generateCollectionClass
// //////////////////////////////////////////////////////////////////////////

/* @var $d EntityRepresentation */
if ($d->generateCollectionClass == true) {
	for($i = 0; $i < 2; $i ++) {
		// We instanciate a sub Flexed
		$sf = new Flexed ();
		$sf->package = $f->package;
		$sf->prefix = $f->prefix;
		$sf->company = $f->company;
		$sf->author = $f->author;
		$sf->projectName = $f->projectName;
		// We qualify the $collectionClassName required by the collection template

		$collectionClassName =  getCollectionClassName($f->prefix,$d->name);
		$tplExtension = "h";
		if ($i == 1) {
			$tplExtension = "m";
		}
		// subTPL execution
		ob_start ();
		include "ObjCObjectCollection.$tplExtension.php";
		$subResult = ob_get_clean ();
		// End of subTPL execution
		$sf->source = $subResult; // We store the generation result
		                          // and the package path
		$sf->packagePath = $destination . $sf->package;
		// We add the flexed the Hypotypose for the post processors
		$h->addFlexed ( $sf );
	}
}

// /////////////////////////////////////////////////////////////////////////
// Relationships imports an Sub generation of collections.
// //////////////////////////////////////////////////////////////////////////
while ( $d->iterateOnProperties () === true ) {
	$property = $d->getProperty ();
	if ($property->instanceOf != null) {
		$instanceOf = $property->instanceOf;
		echoindent ( "@class $instanceOf;\n", 0 );
		
		// SUB GENERATION OF COLLECTIONS
		// we do generate .h and .m in the same sub-loop
		// The sub templates relies on $sf
		
		$pos = strpos ( $instanceOf, COLLECTION_OF );
		if ($pos >= 0) {
			for($i = 0; $i < 2; $i ++) {
				// We instanciate a sub Flexed
				$sf = new Flexed ();
				$sf->package = $f->package;
				$sf->prefix = $f->prefix;
				$sf->company = $f->company;
				$sf->author = $f->author;
				$sf->projectName = $f->projectName;
				// We qualify the $collectionClassName required by the collection template
				$collectionClassName = $instanceOf;
				$tplExtension = "h";
				if ($i == 1) {
					$tplExtension = "m";
				}
				// subTPL execution
				ob_start ();
				include "ObjCObjectCollection.$tplExtension.php";
				$subResult = ob_get_clean ();
				// End of subTPL execution
				$sf->source = $subResult; // We store the generation result
				                          // and the package path
				$sf->packagePath = $destination . $sf->package;
				// We add the flexed the Hypotypose for the post processors
				$h->addFlexed ( $sf );
			}
		}
	}
}
?>

@interface <?php echo getCurrentClassNameFragment($d,$f->prefix)?>:<?php
// We determine the parent class
if ($d->instanceOf != null) {
	echo $d->instanceOf;
} else if ($parentClass != null) {
	echo $parentClass;
} else {
	echo "NSObject";
}
?><?php echo (isset($protocols))?"<$protocols>":""; ?>{
}

<?php
while ( $d->iterateOnProperties () === true ) {
	$property = $d->getProperty ();
	echoindent ( $languageHelper->getPropertyDeclaration ( $property, $allowScalars ), 0 );
}
?>

<?php
while ( $d->iterateOnProperties () === true ) {
	$property = $d->getProperty ();
	if($property->isGeneratedType){
		echoIndent("- (".$property->instanceOf."*)".$property->name."_auto;\n",0);
	}
}
?>

+ (<?php echo getCurrentClassNameFragment($d,$f->prefix);?> *)instanceFromDictionary:(NSDictionary *)aDictionary  inRegistry:(WattRegistry*)registry;
- (<?php echo getCurrentClassNameFragment($d,$f->prefix);?> *)localized;
@end
<?php ////////////   GENERATION ENDS HERE   ////////// ?>