<?xml version="1.0"?>
<FIXML xsi:schemaLocation="http://www.finacle.com/fixml executeFinacleScript.xsd" xmlns="http://www.finacle.com/fixml"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <Header>
        <RequestHeader>
            <MessageKey>
                <RequestUUID>{{ $requestID }}</RequestUUID>
                <ServiceRequestId>executeFinacleScript</ServiceRequestId>
                <ServiceRequestVersion>10.2</ServiceRequestVersion>
                <ChannelId>COR</ChannelId>
                <LanguageId />
            </MessageKey>
            <RequestMessageInfo>
                <BankId>01</BankId>
                <TimeZone />
                <EntityId />
                <EntityType />
                <ArmCorrelationId />
                <MessageDateTime>{{ $disbursement_date }}</MessageDateTime>
            </RequestMessageInfo>
            <Security>
                <Token>
                    <PasswordToken>
                        <UserId />
                        <Password />
                    </PasswordToken>
                </Token>
                <FICertToken />
                <RealUserLoginSessionId />
                <RealUser />
                <RealUserPwd />
                <SSOTransferToken />
            </Security>
        </RequestHeader>
    </Header>

    <Body>
        <executeFinacleScriptRequest>
            <ExecuteFinacleScriptInputVO>
                <requestId>mobi_loan_disb.scr</requestId>
            </ExecuteFinacleScriptInputVO>
            <executeFinacleScript_CustomData>
                <DISB_AMT>{{ $amount }}</DISB_AMT>
                <PHONE_NUMBER>{{ $phone }}</PHONE_NUMBER>
                <NAME>{{ $name }}</NAME>
                <DISB_DATE>{{ $coldate }}</DISB_DATE>
                <LOAN_ID>{{ $loan_id }}</LOAN_ID>
                <TRAN_REF_NUM>{{ $transaction_reference }}</TRAN_REF_NUM>
            </executeFinacleScript_CustomData>
        </executeFinacleScriptRequest>
    </Body>
</FIXML>
