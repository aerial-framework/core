package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.ASN1Type;
    import org.aerialframework.libs.as3crypto.util.asn1.type.PrintableStringType;

    public function printableString(size:int = int.MAX_VALUE, size2:int = 0):ASN1Type
    {
        return new PrintableStringType(size, size2);
    }
}