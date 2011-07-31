package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.ASN1Type;
    import org.aerialframework.libs.as3crypto.util.asn1.type.TeletexStringType;

    public function teletexString(size:int = int.MAX_VALUE, size2:int = 0):ASN1Type
    {
        return new TeletexStringType(size, size2);
    }
}