/**
 * IAsn1Type
 *
 * An interface for Asn-1 types.
 * Copyright (c) 2007 Henri Torgemane
 *
 * See LICENSE.txt for full license information.
 */
package org.aerialframework.libs.as3crypto.util.der
{
    import flash.utils.ByteArray;

    public interface IAsn1Type
    {
        function getType():uint;

        function getLength():uint;

        function toDER():ByteArray;

    }
}