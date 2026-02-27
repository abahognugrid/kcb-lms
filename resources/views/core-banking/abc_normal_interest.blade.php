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
                <MessageDateTime>{{ $system_time }}</MessageDateTime>
            </RequestMessageInfo>
            <Security>
                <Token>
                    <PasswordToken>
                        <UserId />
                        <Password />
                    </PasswordToken>
                </Token>
            </Security>
        </RequestHeader>
    </Header>

    <Body>
        <executeFinacleScriptRequest>
            <ExecuteFinacleScriptInputVO>
                <requestId>mobi_loan_int_book.scr</requestId>
            </ExecuteFinacleScriptInputVO>
            <executeFinacleScript_CustomData>
                <INT_RVB>{{ $amount }}</INT_RVB>
                <BOOK_DATE>{{ $book_date }}</BOOK_DATE>
            </executeFinacleScript_CustomData>
        </executeFinacleScriptRequest>
    </Body>
</FIXML>
