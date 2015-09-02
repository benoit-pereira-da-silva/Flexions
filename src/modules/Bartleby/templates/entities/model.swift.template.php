<?php

require_once FLEXIONS_MODULES_DIR . '/Bartleby/templates/Requires.php';
require_once FLEXIONS_MODULES_DIR . 'Languages/FlexionsSwiftLang.php';

/* @var $f Flexed */
/* @var $d EntityRepresentation */

if (isset ( $f )) {
    // We determine the file name.
    $f->fileName = GenerativeHelperForSwift::getCurrentClassNameWithPrefix($d).'.swift';
    // And its package.
    $f->package = 'iOS/swift/models/';
}

/* TEMPLATES STARTS HERE -> */?>
<?php echo GenerativeHelperForSwift::defaultHeader($f,$d); ?>

import Foundation
import ObjectMapper

// MARK: Model <?php echo ucfirst($d->name)?>

class <?php echo ucfirst($d->name)?> : <?php echo GenerativeHelperForSwift::getBaseClass($f,$d); ?>{
<?php
while ( $d ->iterateOnProperties() === true ) {
    $property = $d->getProperty();
    $name = $property->name;
    if($d->firstProperty()){
        echoIndent(cr(),0);
    }else{
        echoIndent(cr(),0);
    }
    if($property->description!=''){
        echoIndent('//' .$property->description. cr(), 1);
    }
    if($property->type==FlexionsTypes::ENUM){
        $enumTypeName=ucfirst($name);
        echoIndent('enum ' .$enumTypeName.':'.ucfirst($property->instanceOf). '{' . cr(), 1);
        foreach ($property->enumerations as $element) {
            if($property->instanceOf==FlexionsTypes::STRING){
                echoIndent('case ' .ucfirst($element).' = "'.$element.'"' . cr(), 2);
            }else{
                echoIndent('case ' .ucfirst($element).' = '.$element.'' . cr(), 2);
            }
        }
        echoIndent('}' . cr(), 1);
        echoIndent('var ' . $name .':'.$enumTypeName.'?' . cr(), 1);
    }else if($property->type==FlexionsTypes::COLLECTION){
        echoIndent('var ' . $name .':['.ucfirst($property->instanceOf). ']?' . cr(), 1);
    }else if($property->type==FlexionsTypes::OBJECT){
        echoIndent('var ' . $name .':'.ucfirst($property->instanceOf). '?' . cr(), 1);
    }else{
        $nativeType=FlexionsSwiftLang::nativeTypeFor($property->type);
        if(strpos($nativeType,FlexionsTypes::NOT_SUPPORTED)===false){
            echoIndent('var ' . $name .':'.$nativeType. '?' . cr(), 1);
        }else{
            echoIndent('var ' . $name .':Not_Supported = Not_Supported()//'. ucfirst($property->type). cr(), 1);
        }
    }
    if($d->lastProperty()){
        echoIndent(cr(),0);
    }
}?>

<?php if( $modelsShouldConformToNSCoding ) {

    echo('
    // MARK: NSCoding

    required init(coder decoder: NSCoder) {
        super.init(coder: decoder)'.cr());
    GenerativeHelperForSwift::echoBodyOfInitWithCoder($d, 2);
    echo( '
    }

    override func encodeWithCoder(aCoder: NSCoder) {
        super.encodeWithCoder(aCoder)'.cr());
    GenerativeHelperForSwift::echoBodyOfEncodeWithCoder($d, 2);
    echo('
    }
   ');
    }
?>
    override init(){
        super.init()
    }


    // MARK: Mappable

    required init?(_ map: Map) {
        super.init(map)
        mapping(map)
    }

    override class func newInstance(map: Map) -> Mappable? {
        return <?php echo ucfirst($d->name)?>(map)
    }


    override func mapping(map: Map) {
        super.mapping(map)
<?php
while ( $d ->iterateOnProperties() === true ) {
    $property = $d->getProperty();
    $name = $property->name;
    echoIndent($name . ' <- map["' . $name . '"]' . cr(), 2);
}
?>
    }
}


class <?php echo Pluralization::pluralize($d->name).'Collection'?> : <?php echo GenerativeHelperForSwift::getBaseClass($f,$d); ?> {


    var items:[<?php echo ucfirst($d->name)?>]?


    <?php if( $modelsShouldConformToNSCoding ) {
    echo('
    // MARK: NSCoding

    required init(coder decoder: NSCoder) {
        super.init(coder:decoder)
        items=decoder.decodeObjectForKey("items") as? ['.ucfirst($d->name).']
    }

    override func encodeWithCoder(aCoder: NSCoder) {
        aCoder.encodeObject(items,forKey:"items")
    }
');
}
?>
    // MARK: Mappable

    required init?(_ map: Map) {
        super.init(map)
        mapping(map)
    }


    override class func newInstance(map: Map) -> Mappable? {
        return <?php echo Pluralization::pluralize($d->name).'Collection'?>(map)
    }


    override func mapping(map: Map) {
        items <- map["items"]
    }

}

<?php /*<- END OF TEMPLATE */?>