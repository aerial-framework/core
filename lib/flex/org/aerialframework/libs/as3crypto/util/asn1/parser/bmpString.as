package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.BMPStringType;

    public function bmpString(size:int = int.MAX_VALUE, size2:int = 0):BMPStringType
    {
        return new BMPStringType(size, size2);
    }
}