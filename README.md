Scheduled Emails
================

Description
-----------
Regularly send out emails to customers who have bought specific products. 
Automate sending out Magento transactional emails to these customers. 
Schedule the set of transactional emails to go out one day after, one 
week after, one month after, etc. 


How to use
-------------------------
Upload the files to the root of your Magento install. Let the install script 
run. The following tables will be created: 

- aydus_scheduledemails_campaign
- aydus_scheduledemails_campaign_schedule
- aydus_scheduledemails_campaign_customer

In the admin, go to System -> Transactional Emails -> Scheduled Emails. Here you 
will see a grid of campaigns. A campaign is a group of products set of 
transactional emails that are scheduled to be sent over a period of time:

- 1 day after
- 1 week after
- 1 month after
- 3 months after
- 6 months after
- 9 months after
- 1 year after

Create a campaign, set the products attribute sets/skus to trigger on. Basically 
when a customer purchases the product, they will be queued to received the emails.
Select the transactional emails to send to the customer over a period of time.
