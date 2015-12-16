Kanbanize-Slack integration
===========================

This command-line program will poll (via `cron` job) Kanbanize looking for new activity on your boards, group the activity by task and then send [optionally] filtered sets of activity to a list of Slack channels via an "Incoming WebHook".

Installation
------------

1. Configure an "Incoming WebHook" in Slack. This will provide you with a url like `https://hooks.slack.com/services/ABC/DEF/GHI` that you will need later.

2. Get your account-specific Kanbanize API key under **Account >> API** in Kanbanize. You will need this later.

3. Clone this repository and its required submodules:

        git clone https://github.com/middlebury/kanbanize-slack.git
        cd kanbanize-slack
        git submodule update --init

4. Copy the `config.php.example` file to `config.php` and configure your values (more on that below).

5. Add a `cron` job to run the script periodically, e.g. every 5 minutes or so.

   Note that Kanbanize has per-hour & per-key [API request limits](https://kanbanize.com/api/) which at the time of this writing is 30 calls to `get_board_activities` per hour, giving a practical limit of running the script every 2 or 3 minutes for a single board or less often for more boards.

General Configuration
---------------------

The configuration for Kanbanize is pretty simple, just your subdomain, API key, and an array of boards to pull activity from:

* `$config['KANBANIZE']['API_KEY']` - *String* -
   As mentioned above, you can find this in your Kanbanize **Account** pane.

* `$config['KANBANIZE']['SUBDOMAIN']` - *String* - The first part of your kanbanize dashboard URL. For example, a Kanbanize url of `https://mycompany.kanbanize.com/ctrl_board/2` would have a subdomain of `mycompany`.

* `$config['KANBANIZE']['BOARDS']` - *Array of Integers* - Add all of the board-ids for boards you wish to fetch activity from.

You can configure one or more Slack organizations, each with their own name, WebHook URL, and destination channels. This makes the configuration for Slack a little more complicated as we may want to filter activity differently for each channel.

In the most simple case where you want to send all activity, to a single channel in a single organization, create a new organization array with `WEBHOOK_URL` and `DESTINATION` elements. Each `DESTINATION` must have a channel/user/private-group identifier as its key and a `Filter` as its value.

This simple example sends all Kanbanize activity to a single user's private channel:

    $config['SLACK_ORGS']['my_org']['WEBHOOK_URL'] = 'https://hooks.slack.com/services/ABC/DEF/GHI';
    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Everything(),
    );

Channel/user/private group identifiers
--------------------------------------
See the Slack [Incoming WebHooks >> Channel Override](https://api.slack.com/incoming-webhooks) section for more details on specify Slack Channels, Users, and private-groups. Here are some basic guidelines:

* `@username` - A direct-message to a user, will show as coming from the "slackbot" user.
* `#channel-name` - A message to a channel.

Filters
-------
This program provides a range of filters that can be combined in arbitrarily complex ways to route activity information to different destinations.

**Filter_Everything**

*No arguments.*

This is the simplest filter which just sends all activity to the the destination.

Example:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Everything(),
    );

**Filter_Kanbanize_Board**

*Arguments: One or more Board id integers.*

This filter lets you specify a list of board-ids to match against and send to the destination. If ANY of the ids match, the activity will be included.

Example:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Board(2),
    );

Example with multiple board ids:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Board(2, 3, 4),
    );

**Filter_Kanbanize_Assignee**

*Arguments: One or more Kanbanize usernames.*

This filter lets you specify a list of assignees to match against and send to the destination. If ANY of the assignees match, the activity will be included.

Example:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Assignee('johndoe'),
    );

Example with multiple assignees:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Assignee('johndoe', 'janesmith'),
    );

**Filter_Kanbanize_Author**

*Arguments: One or more Kanbanize usernames.*

This filter lets you specify a list of "authors" to match against and send to the destination. Authors are the individuals causing the activity in Kanbanize. If ANY of the authors match, the activity will be included.

Example:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Author('johndoe'),
    );

Example with multiple authors:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@username' => new Filter_Kanbanize_Author('johndoe', 'janesmith'),
    );

**Filter_Not**

*Arguments: A Filter object.*

This Boolean-logic filter allows you to invert another filter, change a match in the other filter from including to excluding the activity.

Example sending all activity not authored by a user:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@johndoe' => new Filter_Not(new Filter_Kanbanize_Author('johndoe')),
    );

**Filter_And**

*Arguments: Two or more Filter objects.*

This Boolean-logic filter allows you to combine the results from other filters and include the activity if ALL of the other filters match.

Example sending activity by a particular user on a particular board:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@johndoe' => new Filter_And(
        new Filter_Kanbanize_Board(2),
        new Filter_Kanbanize_Author('bobsmith')
      ),
    );

**Filter_Or**

*Arguments: Two or more Filter objects.*

This Boolean-logic filter allows you to combine the results from other filters and include the activity if ANY of the other filters match.


More complex filter examples
----------------------------

**Others' activity on my items**

Send activity other make to Kanbanize items assigned to me to my channel:

    $config['SLACK_ORGS']['my_org']['DESTINATIONS'] = array(
      '@slack-username' => new Filter_And(
        new Filter_Kanbanize_Assignee('kanbanize-username'),
        new Filter_Not(new Filter_Kanbanize_Author('kanbanize-username'))
      ),
    );

Copyright and License
---------------------
This software is Copyright © *The President and Fellows of Middlebury College* and is provided as Free Software under the terms of the [GPLv3 (or later) license](http://www.gnu.org/licenses/gpl-3.0.en.html).
