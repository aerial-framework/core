package org.aerialframework.libs.as3crypto.util.asn1.type
{
    import flash.net.registerClassAlias;
    import flash.utils.ByteArray;

    public class NullType extends ASN1Type
    {
        registerClassAlias("org.aerialframework.libs.as3crypto.util.asn1.type.NullType", NullType);

        public function NullType()
        {
            super(ASN1Type.NULL);
        }

        protected override function fromDERContent(s:ByteArray, length:int):*
        {
            return "NULL"; // XXX not okay, but "null" is magic.
        }
    }
}