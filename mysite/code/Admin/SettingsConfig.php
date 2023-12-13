<?php

class SettingsConfig extends DataExtension {

    protected static $db = array(
        'ReportEmailTo' => 'Varchar',
        'ReportEmailFrom' => 'Varchar',
        'ReportEmailSubject' => 'Varchar(255)',
        'ReportEmailContent' => 'HTMLText',
        'WelcomeEmailFrom' => 'Varchar',
        'WelcomeEmailSubject' => 'Varchar(255)',
        'WelcomeEmailContent' => 'HTMLText',
        'LaunchDate' => 'SS_Datetime',
        'LGSEmailTo' => 'Varchar',
        'LGSSubject' => 'Varchar(255)',
        'ContactUsEmailTo' => 'Varchar',
        'ContactUsSubject' => 'Varchar(255)',
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root',
            TabSet::create(
                'Emails',
                'Emails'
            )
        );
        
        $fields->findOrMakeTab('Root.Emails.Report', 'Report');
        $fields->findOrMakeTab('Root.Emails.ContactUs', 'Contact Us');
        $fields->findOrMakeTab('Root.Emails.Welcome', 'Welcome');
        $fields->findOrMakeTab('Root.Emails.LGS', 'LGS');
        
        $fields->addFieldsToTab("Root.Emails.Report", [
            EmailField::create('ReportEmailTo', 'Reported Users Email To'),
            EmailField::create('ReportEmailFrom', 'Reported Users Email From'),
            TextField::create('ReportEmailSubject', 'Reported Users Email Subject'),
            HtmlEditorField::create('ReportEmailContent', 'Reported Users Email Content'),
            LabelField::create('conf_message_tags', "Usable tags: <br>%%USERNAME%% - The reported user's first username<br>%%EMAIL%% - The reported user's email address<br>%%MESSAGE%% - The message from repoting user<br><hr>")
        ]);
        
        $fields->addFieldsToTab("Root.Emails.Welcome", [
            EmailField::create('WelcomeEmailFrom', 'Welcome Users Email From'),
            TextField::create('WelcomeEmailSubject', 'Welcome Users Email Subject'),
            HtmlEditorField::create('WelcomeEmailContent', 'Welcome Users Email Content'),
            LabelField::create('conf_message_tags', "Usable tags: <br>%%USERNAME%% - The user's first username<br><hr>")
        ]);
        
        $fields->addFieldsToTab("Root.Emails.ContactUs", [
            EmailField::create('ContactUsEmailTo', 'Contact Us Email To'),
            TextField::create('ContactUsSubject', 'Contact Us Email Subject'),
        ]);
        
        $fields->addFieldsToTab("Root.Emails.LGS", [
            EmailField::create('LGSEmailTo', 'LGS Email To'),
            TextField::create('LGSSubject', 'LGS Email Subject'),
        ]);

        $fields->addFieldsToTab("Root.Main", [
            DatetimeField::create('LaunchDate')
        ]);
    }
}