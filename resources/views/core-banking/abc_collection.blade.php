<?xml version="1.0"?>
<FIXML xsi:schemaLocation="http://www.finacle.com/fixml executeFinacleScript.xsd"
       xmlns="http://www.finacle.com/fixml"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <RequestHeader>
            <MessageKey>
                <RequestUUID>{{ $requestID }}</RequestUUID>
                <ServiceRequestId>executeFinacleScript</ServiceRequestId>
                <ServiceRequestVersion>10.2</ServiceRequestVersion>
                <ChannelId>COR</ChannelId>
                <LanguageId/>
            </MessageKey>
            <RequestMessageInfo>
                <BankId>01</BankId>
                <MessageDateTime>{{ $disbursement_date }}</MessageDateTime>
            </RequestMessageInfo>
            <Security>
                <Token>
                    <PasswordToken>
                        <UserId/>
                        <Password/>
                    </PasswordToken>
                </Token>
            </Security>
        </RequestHeader>
    </Header>
    <Body>
        <executeFinacleScriptRequest>
            <ExecuteFinacleScriptInputVO>
                <requestId>mobi_loan_coll.scr</requestId>
            </ExecuteFinacleScriptInputVO>
            <executeFinacleScript_CustomData>
                <LOAN_ID>{{ $loan_id }}</LOAN_ID>
                <PRIN_AMT>{{ $principal_amount }}</PRIN_AMT>
                <NORM_INT>{{ $normal_interest }}</NORM_INT>
                <PEN_INT>{{ $penal_interest }}</PEN_INT>
                <INT_INCOME>{{ $interest_income }}</INT_INCOME>
                <NAME>{{ $name }}</NAME>
                <PHONE_NUMBER>{{ $phone }}</PHONE_NUMBER>
                <COLL_DATE>{{ $coldate }}</COLL_DATE>
                <TRAN_REF_NUM>{{ $transaction_reference }}</TRAN_REF_NUM>
            </executeFinacleScript_CustomData>
        </executeFinacleScriptRequest>
    </Body>
</FIXML>
