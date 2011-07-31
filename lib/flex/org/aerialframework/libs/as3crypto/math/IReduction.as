package org.aerialframework.libs.as3crypto.math
{
    internal interface IReduction
    {
        function convert(x:BigInteger):BigInteger;

        function revert(x:BigInteger):BigInteger;

        function reduce(x:BigInteger):void;

        function mulTo(x:BigInteger, y:BigInteger, r:BigInteger):void;

        function sqrTo(x:BigInteger, r:BigInteger):void;
    }
}