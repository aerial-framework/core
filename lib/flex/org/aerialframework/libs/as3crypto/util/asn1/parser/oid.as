package org.aerialframework.libs.as3crypto.util.asn1.parser
{
    import org.aerialframework.libs.as3crypto.util.asn1.type.OIDType;
    import org.aerialframework.libs.as3crypto.util.der.ObjectIdentifier;

    public function oid(...p):OIDType
    {
        var s:String = p.length > 0 ? p.join(".") : null;
        return new OIDType(s);
    }
}