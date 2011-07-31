/**
 * IConnectionState
 *
 * Interface for TLS/SSL Connection states.
 *
 * See LICENSE.txt for full license information.
 */
package org.aerialframework.libs.as3crypto.crypto.tls
{
    import flash.utils.ByteArray;

    public interface IConnectionState
    {
        function decrypt(type:uint, length:uint, p:ByteArray):ByteArray;

        function encrypt(type:uint, p:ByteArray):ByteArray;
    }
}