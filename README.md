# Redmine Time-Entries CSV loader

Can't really find a better name for this. I swear I tried but...
Well, as the name hopefully implies, this little application allows you to bulk-load a CSV file containing time entries into your Redmine account of choice. All you need is the site URL, a valid API consumer key (find it in your Redmine profile) and - guess what - a properly formatted CSV file.

Let's start.

## Requirements

* Unix
* PHP 5.3+
* cURL and PHP cURL extension
* SimpleXML extension
* Bash (if you want to use the lazy-ass _loader_ command)

## Installation

Get the thing from bitbucket:

    git clone git@bitbucket.org:agavee/php-redmine-csv-timeentries.git

I can here your: "Awwwwh, where do ya wanna go with dat folda name?". Nowhere, just wanted to make it clear I'm a sucker.
Time to install deps now:

    cd php-redmine-csv-timeentries
    make setup

This will download all dependencies from the interweb and configure them to work together.
Almost done:

    cp config.ini.dist config.ini
    vi config.ini

WAT? No vi-fu? OK, open `config.ini` with you preferred editor and figure out params like a boss.
Done? OK, that's all. Test the thing out with

    ./loader help load

You should get something like

    Usage:
     load [-d|--dry-run] [-f|--force] input

    Arguments:
     input                 Which file do you want to load? (with path)

    Options:
     --dry-run (-d)        Simulates the loading using console output but does not load content to Redmine
     --force (-f)          Load file contents even if the file has already been loaded *somewhen* in the past
     --help (-h)           Display this help message.
     --quiet (-q)          Do not output any message.
     --verbose (-v)        Increase verbosity of messages.
     --version (-V)        Display this application version.
     --ansi                Force ANSI output.
     --no-ansi             Disable ANSI output.
     --no-interaction (-n) Do not ask any interactive question.

You're ready to rock!

## Writing your CSV file

Writing CSV files by hand sucks, but even if you use a spreadsheet and export data in CSV, it's important to keep those convention in mind:

* Field separator: ,
* Field delimiter: "
* Enctype: UTF-8

That said, the very first line *MUST* (hey... really, it *MUST*) contain what follows:

    "issue_id","project_id","spent_on","hours","activity_id","comments"

If you're working in a spreadsheet you'll simply put each label delimited by "..." in its own column, in the first line.
The following lines should contain actual data. Odds are good you already know all details but if in doubt, take a look here: http://www.redmine.org/projects/redmine/wiki/Rest_TimeEntries
Remember that while both @issue_id@ and @activity_id@ are integers, @project_id@ is a string identifing your project. You could spot it in the project URL.

    http://my.tracker.url/projects/my-beloved-project
                                       ^
                                       |
                                       ---------- hey, it's me! ~_~ cute uh?!

Now name your file what the hell you see fit (my suggestion: go for a date identifier, such as 2013-may.csv or 2013-week13.csv etc) and put it in @data@ directory.

## Usage

Move to installation dir then run

    ./loader load --dry-run data/your-file-name.csv

this will simulate the loading and give you complete feedback on that would be sent to the server (in JSON format) (with counter) (/me rocks!).
When you feel ready, launch

    ./loader load data/your-file-name.csv

Use @-v@ or @--verbose@ to receive an output similar to the one before.

OK, you should be server.
Bye.

## Troubleshooting and notes

*TBD*

## ToDo

Moving done files in another folder should be safer to avoid accidental double-inserts.