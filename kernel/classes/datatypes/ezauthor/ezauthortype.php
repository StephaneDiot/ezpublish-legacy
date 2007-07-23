<?php
//
// Definition of eZAuthorType class
//
// Created on: <19-Aug-2002 10:51:10 bf>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.10.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*!
  \class eZAuthorType ezauthortype.php
  \ingroup eZDatatype
  \brief eZAuthorType handles multiple authors

*/

include_once( "kernel/classes/ezdatatype.php" );
include_once( "kernel/classes/datatypes/ezauthor/ezauthor.php" );
include_once( "lib/ezutils/classes/ezmail.php" );
include_once( 'lib/ezutils/classes/ezstringutils.php' );

define( "EZ_DATATYPESTRING_AUTHOR", "ezauthor" );

class eZAuthorType extends eZDataType
{
    function eZAuthorType()
    {
        $this->eZDataType( EZ_DATATYPESTRING_AUTHOR, ezi18n( 'kernel/classes/datatypes', "Authors", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $actionRemoveSelected = false;
        if ( $http->hasPostVariable( 'CustomActionButton' ) )
        {
            $customActionArray = $http->postVariable( 'CustomActionButton' );

            if ( isset( $customActionArray[$contentObjectAttribute->attribute( "id" ) . '_remove_selected'] ) )
                if ( $customActionArray[$contentObjectAttribute->attribute( "id" ) . '_remove_selected'] == 'Remove selected' )
                    $actionRemoveSelected = true;
        }

        if ( $http->hasPostVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            $idList = $http->postVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) );
            $nameList = $http->postVariable( $base . "_data_author_name_" . $contentObjectAttribute->attribute( "id" ) );
            $emailList = $http->postVariable( $base . "_data_author_email_" . $contentObjectAttribute->attribute( "id" ) );

            if ( $http->hasPostVariable( $base . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" ) ) )
                $removeList = $http->postVariable( $base . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" ) );
            else
                $removeList = array();

            if ( $contentObjectAttribute->validateIsRequired() )
            {
                if ( trim( $nameList[0] ) == "" )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'At least one author is required.' ) );
                    return EZ_INPUT_VALIDATOR_STATE_INVALID;
                }
            }
            if ( trim( $nameList[0] ) != "" )
            {
                for ( $i=0;$i<count( $idList );$i++ )
                {
                    if ( $actionRemoveSelected )
                        if ( in_array( $idList[$i], $removeList ) )
                            continue;

                    $name =  $nameList[$i];
                    $email =  $emailList[$i];
                    if ( trim( $name )== "" )
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The author name must be provided.' ) );
                        return EZ_INPUT_VALIDATOR_STATE_INVALID;

                    }
                    $isValidate =  eZMail::validate( $email );
                    if ( ! $isValidate )
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The email address is not valid.' ) );
                        return EZ_INPUT_VALIDATOR_STATE_INVALID;
                    }
                }
            }
        }
        else
        {
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'At least one author is required.' ) );
                return EZ_INPUT_VALIDATOR_STATE_INVALID;
            }
        }
        return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
    }

    /*!
     Store content
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $author->xmlString() );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion, &$originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $author = new eZAuthor( );

        if ( trim( $contentObjectAttribute->attribute( "data_text" ) ) != "" )
        {
            $author->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
            $temp = $contentObjectAttribute->attribute( "data_text");
        }
        else
        {
            $user = eZUser::currentUser();
            $userobject = $user->attribute( 'contentobject' );
            if ( $userobject )
            {
                $author->addAuthor( $userobject->attribute( 'id' ), $userobject->attribute( 'name' ), $user->attribute( 'email' ) );
            }
         }

        if ( count( $author->attribute( 'author_list' ) ) == 0 )
        {
//             $author->addAuthor( "Default", "" );
        }

        return $author;
    }


    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        if ( !$author )
            return false;

        return $author->metaData();
    }

    function toString( $contentObjectAttribute )
    {
        $authorList = array();
        $content = $contentObjectAttribute->attribute( 'content' );
        foreach ( $content->attribute( 'author_list') as $author )
        {
            $authorList[] = eZStringUtils::implodeStr( array( $author['name'], $author['email'],$author['id'] ), '|' );
        }
        return eZStringUtils::implodeStr( $authorList, "&" );
    }

    function fromString( &$contentObjectAttribute, $string )
    {
        $authorList = eZStringUtils::explodeStr( $string, '&' );

        $author = new eZAuthor( );


        foreach ( $authorList as $authorStr )
        {
            $authorData = eZStringUtils::explodeStr( $authorStr, '|' );
            $author->addAuthor( $authorData[2], $authorData[0], $authorData[1] );

        }
        $contentObjectAttribute->setContent( $author );
        return $author;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $authorIDArray = $http->postVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) );
            $authorNameArray = $http->postVariable( $base . "_data_author_name_" . $contentObjectAttribute->attribute( "id" ) );
            $authorEmailArray = $http->postVariable( $base . "_data_author_email_" . $contentObjectAttribute->attribute( "id" ) );

            $author = new eZAuthor( );

            $i = 0;
            foreach ( $authorIDArray as $id )
            {
                $author->addAuthor( $authorIDArray[$i], $authorNameArray[$i], $authorEmailArray[$i] );
                $i++;
            }
            $contentObjectAttribute->setContent( $author );
        }
        return true;
    }

    /*!
    */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case "new_author" :
            {
                $author = $contentObjectAttribute->content( );

                $author->addAuthor( -1, "", "" );
                $contentObjectAttribute->setContent( $author );
            }break;
            case "remove_selected" :
            {
                $author = $contentObjectAttribute->content( );
                $postvarname = "ContentObjectAttribute" . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" );
                if ( !$http->hasPostVariable( $postvarname ) )
                    break;
                $array_remove = $http->postVariable( $postvarname );

                $author->removeAuthors( $array_remove );
                $contentObjectAttribute->setContent( $author );
            }break;
            default :
            {
                eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZAuthorType" );
            }break;
        }
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content( );
        $authorList = $author->attribute( 'author_list' );
        return count( $authorList ) > 0;
    }

    /*!
     Returns the string value.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $author = $contentObjectAttribute->content( );
        $name = $author->attribute( 'name' );
        if ( trim( $name ) == '' )
        {
            $authorList = $author->attribute( 'author_list' );
            if ( is_array( $authorList ) and isset( $authorList[0]['name'] ) )
            {
                $name = $authorList[0]['name']; // Get the first name of Auhtors
                $author->setName( $name );
            }
        }
        return $name;
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return true;
    }

    /*!
     \reimp
     \param package
     \param content attribute

     \return a DOM representation of the content object attribute
    */
    function serializeContentObjectAttribute( &$package, &$objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $dom = new DOMDocument();
        $success = $dom->loadXML( $objectAttribute->attribute( 'data_text' ) );

        $nodeDOM = $node->ownerDocument;
        $importedElement = $nodeDOM->importNode( $dom->documentElement, true );
        $node->appendChild( $importedElement );

        return $node;
    }

    /*!
     \reimp

     \param package
     \param contentobject attribute object
     \param domnode object
    */
    function unserializeContentObjectAttribute( &$package, &$objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'ezauthor' )->item( 0 );
        $xmlString = $rootNode->ownerDocument->saveXML( $rootNode );
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

}

eZDataType::register( EZ_DATATYPESTRING_AUTHOR, "ezauthortype" );

?>
