<?php
    require(__DATAGEN_CLASSES__ . '/NarroContextGen.class.php');
    require_once(__DOCROOT__ . __SUBDIRECTORY__ . '/narro_text_list.php');

    /**
    * The NarroContext class defined here contains any
    * customized code for the NarroContext class in the
    * Object Relational Model.  It represents the "narro_context" table
    * in the database, and extends from the code generated abstract NarroContextGen
    * class, which contains all the basic CRUD-type functionality as well as
    * basic methods to handle relationships and index-based loading.
    *
    * @package Narro
    * @subpackage DataObjects
    *
    */
    class NarroContext extends NarroContextGen {
        /**
        * Default "to string" handler
        * Allows pages to _p()/echo()/print() this object, and to define the default
        * way this object would be outputted.
        *
        * Can also be called directly via $objNarroContext->__toString().
        *
        * @return string a nicely formatted string representation of this object
        */
        public function __toString() {
            return sprintf('NarroContext Object %s',  $this->intContextId);
        }

    }
?>