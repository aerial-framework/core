package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.IA5StringType;

    public function ia5String(size:int = int.MAX_VALUE, size2:int = 0):IA5StringType
    {
        return new IA5StringType(size, size2);
    }
}