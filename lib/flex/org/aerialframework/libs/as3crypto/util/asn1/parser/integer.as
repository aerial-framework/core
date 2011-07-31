package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.ASN1Type;
    import org.aerialframework.libs.as3crypto.util.asn1.type.IntegerType;

    public function integer():ASN1Type
    {
        return new IntegerType;
    }
}