Feature: I can create and validate signed URLs

    Scenario: I can create a signed URL from the signer service
        When I create a signed URL
        Then I should get a valid signed URL

    Scenario: I can access a signed route with a valid signed URL
        When I create a signed URL
        And I request the signed URL
        Then I should receive a successful response

    Scenario: I cannot access a signed route without a valid signed URL
        When I request a signed route without a valid signature
        Then I should receive a forbidden response

    Scenario: I can create an absolute signed URL from the signer service
        When I create an absolute signed URL
        Then I should get a valid signed URL

    Scenario: I can access a signed route with a valid absolute signed URL
        When I create an absolute signed URL
        And I request the signed URL
        Then I should receive a successful response
