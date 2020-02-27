# docs.classicpress.net

This repository contains the files for https://docs.classicpress.net/.

## Local development

Download the site files and contact a site administrator (probably James) for a
recent database dump.

Load the database dump into a MySQL database on your computer.  The site is
configured using a `.env` file, so you can copy `.env.example` to `.env` and
fill in your database values there.

You will need to
[install `composer`](https://getcomposer.org/download/)
and run `composer install` in order to download the library that makes the
`.env` file work properly.

Then update the site URL, for example using
[WP-CLI](https://wp-cli.org/):

```
wp search-replace https://docs.classicpress.net http://docs.classicpress.local
```

Finally add a local administrative user, for example:

```
wp user create admin admin@local.host --role=administrator --user_pass=changeme
```

## Markdown sync

This site is set up to pull in content for most of its pages from the
https://github.com/ClassicPress/ClassicPress-docs repository.

See the
[`wp-content/mu-plugins/markdown-sync`](wp-content/mu-plugins/markdown-sync)
subdirectory for more information about this setup and how to make it work
during local development.

