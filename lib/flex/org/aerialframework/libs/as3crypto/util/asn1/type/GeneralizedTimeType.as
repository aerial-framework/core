package org.aerialframework.libs.as3crypto.util.asn1.type
{
    import flash.net.registerClassAlias;

    public class GeneralizedTimeType extends ASN1Type
    {
        registerClassAlias("org.aerialframework.libs.as3crypto.util.asn1.GeneralizedTime", GeneralizedTimeType);

        public function GeneralizedTimeType()
        {
            super(ASN1Type.GENERALIZED_TIME);
        }

    }
}