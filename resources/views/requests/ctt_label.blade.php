<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:tem="http://tempuri.org/"
    xmlns:ctt="http://schemas.datacontract.org/2004/07/CTTExpressoWS"
    xmlns:ctt1="http://schemas.datacontract.org/2004/07/CTTExpressoWS.Models.ShipmentProvider"
    xmlns:ctt2="http://schemas.datacontract.org/2004/07/CTTExpressoWS.Models.ShipmentProvider.SEPs">
    <soapenv:Header />
    <soapenv:Body>
        <tem:CreateShipment>
            <tem:Input>
                <ctt:AuthenticationID>{{ $token['authentication_id'] }}</ctt:AuthenticationID>
                <ctt:DeliveryNote>
                    <ctt1:ClientId>{{ $token['client_id'] }}</ctt1:ClientId>
                    <ctt1:ContractId>{{ $token['contract_id'] }}</ctt1:ContractId>
                    <ctt1:DistributionChannelId>99</ctt1:DistributionChannelId>
                    <ctt1:ExtData>?</ctt1:ExtData>
                    <ctt1:ShipmentCTT>
                        <ctt1:ShipmentCTT>
                            <ctt1:HasSenderInformation>false</ctt1:HasSenderInformation>
                            <ctt1:ReceiverData>
                                <ctt1:Address>{{ $data['receiver_address_street'] }}</ctt1:Address>
                                <ctt1:City>{{ $data['receiver_address_city'] }}</ctt1:City>
                                <ctt1:ContactName>{{ $data['receiver_contact_name'] }}</ctt1:ContactName>
                                <ctt1:Country>{{ $data['country'] }}</ctt1:Country>
                                <ctt1:Email>{{ $data['receiver_email'] }}</ctt1:Email>
                                <ctt1:MobilePhone>{{ $data['receiver_contact_phone'] }}</ctt1:MobilePhone>
                                <ctt1:Name>{{ $data['receiver_name'] }}</ctt1:Name>
                                <ctt1:PTZipCode3>{{ $data['receiver_address_postal_code2'] }}</ctt1:PTZipCode3>
                                <ctt1:PTZipCode4>{{ $data['receiver_address_postal_code1'] }}</ctt1:PTZipCode4>
                                <ctt1:PTZipCodeLocation>{{ $data['receiver_address_city'] }}</ctt1:PTZipCodeLocation>
                                <ctt1:Type>Receiver</ctt1:Type>
                            </ctt1:ReceiverData>
                            <ctt1:SenderData>
                                <ctt1:Address>{{ $data['sender_address_street'] }}</ctt1:Address>
                                <ctt1:City>{{ $data['sender_address_city'] }}</ctt1:City>
                                <ctt1:ContactName>{{ $data['sender_contact_name'] }}</ctt1:ContactName>
                                <ctt1:Country>{{ $data['country'] }}</ctt1:Country>
                                <ctt1:Door>99</ctt1:Door>
                                <ctt1:Email>{{ $data['sender_email'] }}</ctt1:Email>
                                <ctt1:MobilePhone>{{ $data['sender_contact_phone'] }}</ctt1:MobilePhone>
                                <ctt1:Name>{{ $data['sender_name'] }}</ctt1:Name>
                                <ctt1:NonPTZipCodeLocation />
                                <ctt1:PTZipCode3>{{ $data['sender_address_postal_code2'] }}</ctt1:PTZipCode3>
                                <ctt1:PTZipCode4>{{ $data['sender_address_postal_code1'] }}</ctt1:PTZipCode4>
                                <ctt1:PTZipCodeLocation>{{ $data['sender_address_city'] }}</ctt1:PTZipCodeLocation>
                                <ctt1:Type>Sender</ctt1:Type>
                            </ctt1:SenderData>
                            <ctt1:ShipmentData>
                                <ctt1:ATCode>{{ $data['order_code'] }}</ctt1:ATCode>
                                <ctt1:ClientReference>{{ $data['order_code'] }}</ctt1:ClientReference>
                                <ctt1:IsDevolution>false</ctt1:IsDevolution>
                                <ctt1:Observations>{{ $data['receiver_instructions'] }}</ctt1:Observations>
                                <ctt1:Quantity>{{ $data['number_of_volumes'] }}</ctt1:Quantity>
                                <ctt1:Weight>{{ $data['weight'] }}</ctt1:Weight>
                            </ctt1:ShipmentData>
                            <ctt1:SpecialServices>
                                <ctt1:SpecialService>
                                    <ctt1:MultipleHomeDelivery>
                                        <ctt2:AttemptsNumber>1</ctt2:AttemptsNumber>
                                        <ctt2:InNonDeliveryCase>PostOfficeNotiffied</ctt2:InNonDeliveryCase>
                                    </ctt1:MultipleHomeDelivery>
                                    <ctt1:SpecialServiceType>MultipleHomeDelivery</ctt1:SpecialServiceType>
                                </ctt1:SpecialService>
                            </ctt1:SpecialServices>
                        </ctt1:ShipmentCTT>
                    </ctt1:ShipmentCTT>
                    <ctt1:SubProductId>{{ $data['service_type'] }}</ctt1:SubProductId>
                </ctt:DeliveryNote>
                <ctt:RequestID>d1f1100b-000c-4ba4-b000-d0fae0d1b000</ctt:RequestID>
                <ctt:UserID>{{ $token['user_id'] }}</ctt:UserID>
            </tem:Input>
        </tem:CreateShipment>
    </soapenv:Body>
</soapenv:Envelope>