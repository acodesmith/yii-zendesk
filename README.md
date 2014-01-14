yii-zendesk
===========

Yii Extension for the ZenDesk API


===========

Class Structure
===========

 * token - API token set in config.
 * email - Email associated with zendesk account.
 * subdomain - Zendesk Subdomain assocated with account. (Example:foobar - http://foobar.zendesk.com)
 * baseUrl - User in curlWrap. Built from subdomain.
 * zenDeskUrl - Used to generate zendesk links.
 * statuses - Array of status options provided by zendesk.
 * init - Required for Yii CApplicationComponent lifecycle.
 * tickets - Returns Tickets - optional param added scope.
 * getTicket - Get ticket by ticket ID
 * users - Returns Users - optional param added scope.
 * getUserLink - Returns link for user account on zendesk.com
 * search - Builds param based search query from key value pair. (Uses Url Encoded json object)
 * findTicketsByEmailCriteria - Helper function which returns criteria for all tickets associated with email.
 * findUserByEmailCriteria  - Helper function which returns criteria for all users associated with email.
 * curlWrap - Curl Wrapper for Curling ZenDesk API
 * separateTicketsByStatus - Helpers function for displaying tickers by status.