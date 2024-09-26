<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
    <soapenv:Header />
    <soapenv:Body>
        <tem:GetEventosObjectos_V3>
            <tem:ID>{{ $token['authentication_id'] }}</tem:ID>
            <tem:NObjectos>
                <arr:string>{{ $tracking }}</arr:string>
            </tem:NObjectos>
        </tem:GetEventosObjectos_V3>
    </soapenv:Body>
</soapenv:Envelope>