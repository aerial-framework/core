package org.aerialframework.libs.as3crypto.util.asn1.type
{
    import flash.net.registerClassAlias;

    public class BooleanType extends ASN1Type
    {
        registerClassAlias("org.aerialframework.libs.as3crypto.util.asn1.BooleanType", BooleanType);
        public function BooleanType()
        {
            super(ASN1Type.BOOLEAN);
        }
    }
}