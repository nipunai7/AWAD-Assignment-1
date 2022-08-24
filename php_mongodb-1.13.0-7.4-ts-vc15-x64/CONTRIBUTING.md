# Contributing to the PHP Driver for MongoDB

## Building from Source

Developers who would like to contribute to the driver will need to build it from
source. The repository may be initialized with:

```
$ git clone https://github.com/mongodb/mongo-php-driver.git
$ cd mongo-php-driver
$ git submodule update --init
```

The following script may be used to build the driver:

```
#!/bin/sh

phpize > /dev/null && \
./configure --enable-mongodb-developer-flags > /dev/null && \
make clean > /dev/null && make all > /dev/null && make install
```

## Testing

The extension's test use the PHPT format from PHP internals. This format is
documented in the following links:

 * [Introduction to PHPT Files](https://qa.php.net/write-test.php)
 * [PHPT - Test File Layout](https://qa.php.net/phpt_details.php)

Generally, most tests will be based on the following template:

```
--TEST--
Description of API or JIRA issue being tested
--SKIPIF--
<?php require __DIR__ . "/../utils/basic-skipif.inc"; ?>
<?php /* One or more skip functions */ ?>
--FILE--
<?php
require_once __DIR__ . "/../utils/basic.inc";

// Test code

?>
===DONE===
<?php exit(0); ?>
--EXPECT--
===DONE===
```

The `basic-skipif.inc` and `basic.inc` files contain utility functions for the
`SKIPIF` and `FILE` sections, respectively. If those functions are not needed
(e.g. skip logic only depends on checking the `PHP_INT_SIZE` constant), the test
should not include the file. When it doubt, keep it simple.

### Best Practices for `SKIPIF`

The [`skipif.php`](tests/utils/skipif.php) file defines various helper functions
for use within a test's [`SKIPIF`](https://qa.php.net/phpt_details.php#skipif_section)
section. When multiple functions are used in a single `SKIPIF` section, they
should be logically ordered:

 * Any PHP environment requirements should be checked first. For example, if a
   test requires a 64-bit architecture, start by checking `PHP_INT_SIZE` before
   anything else.
 * Any extension build requirements (e.g. `skip_if_not_libmongoc_crypto()`) or
   test environment requirements (e.g. `skip_if_auth()`) should then be checked.
   These functions only examine local information, such as `phpinfo()` output or
   the structure of the `URI` constant, and do not interact with a remote
   MongoDB server.
 * Any remote server requirements should then be checked. A general integration
   test that requires any type of remote server to be accessible might use
   `skip_if_not_live()` while a test requiring a replica set would prefer
   `skip_if_not_replica_set()`.
 * After requiring a remote server to be accessible (optionally with a specific
   type), you can enforce requirements about that server. This includes checking
   its server version, storage engine, availability of test commands, etc.
 * Finally, use `skip_if_not_clean()` if needed to ensure that the collection(s)
   under test are dropped before the test runs.

As a rule of thumb, your `SKIPIF` logic should be written to allow the test to
run in as many environments as possible. To paraphrase the
[robustness principal](https://en.wikipedia.org/wiki/Robustness_principle):

> Be conservative in what/how you test, and liberal in what environment you require

Consider that a well-crafted `EXPECTF` section may allow a `SKIPIF` section to
be less restrictive.

### Environment Variables

The test suite references the following environment variables:

 * `MONGODB_URI`: Connection string. Defaults to `mongodb://127.0.0.1/`, which
   assumes a MongoDB server is listening localhost port 27017.
 * `MONGO_ORCHESTRATION_URI`: API endpoint for Mongo Orchestration. Defaults to
   `http://localhost:8889/v1`. This is only used by a few tests that start
   temporary servers, and those tests will be skipped if Mongo Orchestration is
   inaccessible.
 * `MONGODB_DATABASE`: Default database to use in tests. Defaults to `phongo`.
 * `SSL_DIR`: Path to directory containing certificate files. On Evergreen, this
   will be set to the
   [.evergreen/x509gen](https://github.com/mongodb-labs/drivers-evergreen-tools/tree/master/.evergreen/x509gen)
   directory within
   [drivers-evergreen-tools](https://github.com/mongodb-labs/drivers-evergreen-tools).
   If undefined or inaccessible, tests requiring certificates will be skipped.
 * `API_VERSION`: If defined, this value will be used to construct a
   [`MongoDB\Driver\ServerApi`](https://www.php.net/manual/en/mongodb-driver-serverapi.construct.php),
   which will then be specified as the `serverApi` driver option for
   [`MongoDB\Driver\Manager`](https://www.php.net/manual/en/class.mongodb-driver-manager.php)
   objects created by the test suite.

### Mongo Orchestration

[Mongo Orchestration](https://github.com/10gen/mongo-orchestration) is an HTTP
server that provides a REST API for managing MongoDB servers and clusters.
Evergreen CI and GitHub Actions use configurations provided by the
[drivers-evergreen-tools](https://github.com/mongodb-labs/drivers-evergreen-tools)
repository. These configurations are loaded by Mongo Orchestration, which then
provides a connection string to assign to `MONGODB_URI` and run the test suite.
Additionally, some tests start temporary servers and interact directly with
Mongo Orchestration (via `MONGO_ORCHESTRATION_URI`).

For local development, running Mongo Orchestration is not required and it is
generally sufficient to test against a single-node replica set.

## Updating libmongoc, libbson, and libmongocrypt

The PHP driver can use either system libraries or bundled versions of
libmongoc, libbson, and libmongocrypt. If a new version of either library is
available, the submodule and build configuration will need to be updated to
reflect the new sources and/or package version.

### Updating libmongoc and libbson

#### Update libmongoc submodule

```
$ cd src/libmongoc
$ git fetch
$ git checkout 1.20.0
```

During development, it may be necessary to temporarily point the libmongoc
submodule to a commit on the developer's fork of libmongoc. For instance, the
developer may be working on a PHP driver feature that depends on an unmerged
pull request to libmongoc. In this case, `git remote add` can be used to add
the fork before fetching and checking out the target commit. Additionally, the
submodule path in
[`.gitmodules`](https://github.com/mongodb/mongo-php-driver/blob/master/.gitmodules)
must also be updated to refer to the fork.

#### Ensure libmongoc version information is correct

The build process for Autotools and Windows rely on
`src/LIBMONGOC_VERSION_CURRENT` to infer version information for libmongoc and
libbson. This file can be regenerated using the following Makefile target:

```
$ make libmongoc-version-current
```

Alternatively, the `build/calc_release_version.py` script in libmongoc can be
executed directly.

Note: If the libmongoc submodule points to a non-release, non-master branch, the
script may fail to correctly detect the version. This issue is being tracked in
[CDRIVER-3315](https://jira.mongodb.org/browse/CDRIVER-3315) and can be safely
ignored since this should only happen during development (any PHP driver release
should point to a tagged libmongoc release).

#### Update sources in build configurations

The Autotools and Windows build configurations (`config.m4` and `config.w32`,
respectively) define several variables (e.g. `PHP_MONGODB_MONGOC_SOURCES`) that
collectively enumerate all of the the sources within the libmongoc submodule to
include in a bundled build.

These variables should each have a shell command in a preceding comment, which
should be run to regenerate that particular list of source files. Each command
may be run manually or `scripts/update-submodule-sources.php` may be used to
update all variables. In the event that either libmongoc or libbson introduce a
new source directory, that will need to be manually added (follow prior art).

#### Update package dependencies

The Autotools configuration additionally includes some `pkg-config` commands for
using libmongoc and libbson as system libraries (in lieu of a bundled build).
When bumping the libmongoc version, be sure to update the version check _and_
error message in the `pkg-config` blocks for both libmongoc and libbson.

For example, the following lines might be updated for libmongoc:

```
if $PKG_CONFIG libmongoc-1.0 --atleast-version 1.20.0; then

...

AC_MSG_ERROR(system libmongoc must be upgraded to version >= 1.20.0)
```

#### Update tested versions in Evergreen configuration

Evergreen tests against multiple versions of libmongoc. When updating to a newer
libmongoc version, make sure to update the `libmongoc-version` build axis in
`.evergreen/config.yml`. In general, we test against two additional versions of
libmongoc:

- The upcoming patch release of the current libmongoc minor version (e.g. the
  `r1.x` branch)
- The upcoming minor release of libmongoc (e.g. the `master` branch)

#### Update sources in PECL package generation script

If either libmongoc or libbson introduce a new source directory, that may also
require updating the glob patterns in the `bin/prep-release.php` script to
ensure new source files will be included in any generated PECL package.

#### Test and commit your changes

Verify that the upgrade was successful by ensuring that the driver can compile
using both the bundled sources and system libraries for libmongoc and libbson,
and by ensuring that the test suite passes. Once done, commit the changes to all
of the above files/paths. For example:

```
$ git commit -m "Bump libmongoc to 1.20.0" config.m4 config.w32 src/libmongoc src/LIBMONGOC_VERSION_CURRENT
```

### Updating libmongocrypt

To update libmongocrypt, the steps are similar to the above:

```
$ cd src/libmongocrypt
$ git fetch
$ git checkout 1.3.0
$ make libmongocrypt-version-current
```

Package dependencies in  `config.m4` must also be updated (either manually or
with `scripts/update-submodule-sources.php`), as do the sources in the PECL
generation script.

## Releasing

The follow steps outline the release process for a maintenance branch (e.g.
releasing the `vX.Y` branch as X.Y.Z).

### Ensure PHP version compatibility

Ensure that the library test suite completes on supported versions of PHP.

### Ensure Windows compatibility

PECL will create Windows DLLs for new releases; however, you must ensure that
the extension successfully builds on Windows before releasing. Windows builds
are tested by [AppVeyor](.appveyor.yml).

See the [internals wiki](https://wiki.php.net/internals/windows/stepbystepbuild)
for more information.

### Transition JIRA issues and version

All issues associated with the release version should be in the "Closed" state
and have a resolution of "Fixed". Issues with other resolutions (e.g.
"Duplicate", "Works as Designed") should be removed from the release version so
that they do not appear in the release notes.

Check the corresponding ".x" fix version to see if it contains any issues that
are resolved as "Fixed" and should be included in this release version.

Update the version's release date and status from the
[Manage Versions](https://jira.mongodb.org/plugins/servlet/project-config/PHPC/versions)
page.

### Update version info

The PHP driver uses [semantic versioning](http://semver.org/). Do not break
backwards compatibility in a non-major release or your users will kill you.

Before proceeding, ensure that the `master` branch is up-to-date with all code
changes in this maintenance branch. This is important because we will later
merge the ensuing release commits up to master with `--strategy=ours`, which
will ignore changes from the merged commits.

Update the version and stability constants in `phongo_version.h`. This should
entail removing the version's "-dev" suffix, changing the stability to
"stable", and increasing the last digit for `PHP_MONGO_VERSION_DESC`:

```
#define PHP_MONGODB_VERSION "1.1.8-dev"
#define PHP_MONGODB_STABILITY "devel"
#define PHP_MONGODB_VERSION_DESC 1,1,8,0
```

The above would be changed to:

```
#define PHP_MONGODB_VERSION "1.1.8"
#define PHP_MONGODB_STABILITY "stable"
#define PHP_MONGODB_VERSION_DESC 1,1,8,1
```

The Makefile targets for creating the PECL package depend on these constants, so
you must rebuild the extension after updating `phongo_version.h`.

> **Note:** If this is an alpha or beta release, the version string should
> include the X.Y.Z version followed by the stability and an increment. For
> instance, the first beta release in the 1.4.0 series would be "1.4.0beta1".
> Alpha and beta releases use "alpha" and "beta" stability strings,
> respectively. Release candidates (e.g. "1.4.0RC1") also use "beta" stability.
> See [Documenting release stability and API stability](https://pear.php.net/manual/en/guide.developers.package2.stability.php)
> for more information. For each change to the suffixes of
> `PHP_MONGODB_VERSION`, increment the last digit of
> `PHP_MONGODB_VERSION_DESC`.

### Build PECL package

Create the PECL package description file with `make package.xml`. This creates
a `package.xml` file from a template. Version, author, and file information will
be filled in, but release notes must be copied manually from JIRA.

After copying release notes, use `make package` to create the package file (e.g.
`mongodb-X.Y.Z.tgz`) and ensure that it can be successfully installed:

```
$ pecl install -f mongodb-X.Y.Z.tgz
```

### Commit version update and release notes

Commit the modified `phongo_version.h` file as "Package X.Y.Z"

```
$ git add phongo_version.h
$ git commit -m "Package X.Y.Z"
```

### Tag release

The previous commit will be the target for our release tag:

```
$ git tag -a -m "Release X.Y.Z" X.Y.Z
```

### Update version info back to dev

After tagging, the version and stability constants in `phongo_version.h` should be
updated back to development status.

```
#define PHP_MONGODB_VERSION "1.1.8"
#define PHP_MONGODB_STABILITY "stable"
#define PHP_MONGODB_VERSION_DESC 1,1,8,1
```

The above would be changed to:

```
#define PHP_MONGODB_VERSION "1.1.9-dev"
#define PHP_MONGODB_STABILITY "devel"
#define PHP_MONGODB_VERSION_DESC 1,1,9,0
```

Commit this change:

```
$ git commit -m "Back to -dev" phongo_version.h
```

> **Note:** If this is an alpha, beta, or RC release, the version string should
> increment the stability sequence instead of the patch version. For example,
> if the constants were originally "1.4.0-dev" and "devel" and then changed to
> "1.4.0beta1" and "beta" for the first beta release, this step would see them
> ultimately changed to "1.4.0beta2-dev" and "devel".

### Push commits and tags

```
$ git push
$ git push --tags
```

### Release PECL package

The PECL package may be published via the
[Release Upload](https://pecl.php.net/release-upload.php) form. You will have
one chance to confirm the package information after uploading.

### Merge the maintenance branch up to master

```
$ git checkout master
$ git merge vX.Y --strategy=ours
$ git push
```

The `--strategy=ours` option ensures that all changes from the merged commits
will be ignored.

### Publish release notes

The following template should be used for creating GitHub release notes via
[this form](https://github.com/mongodb/mongo-php-driver/releases/new). The PECL
package may also be attached to the release notes.

```
The PHP team is happy to announce that version X.Y.Z of the [mongodb](https://pecl.php.net/package/mongodb) PHP extension is now available on PECL.

**Release Highlights**

<one or more paragraphs describing important changes in this release>

A complete list of resolved issues in this release may be found at:
$JIRA_URL

**Documentation**

Documentation is available on PHP.net:
https://www.php.net/set.mongodb

**Installation**

You can either download and install the source manually, or you can install the extension with:

    pecl install mongodb-X.Y.Z

or update with:

    pecl upgrade mongodb-X.Y.Z

Windows binaries are available on PECL:
https://pecl.php.net/package/mongodb
```

> **Note:** If this is an alpha or beta release, the installation examples
> should refer to the exact version (e.g. "mongodb-1.8.0beta2"). This is necessary
> because PECL prioritizes recent, stable releases over any stability preferences
> (e.g. "mongodb-beta").

The URL for the list of resolved JIRA issues will need to be updated with each
release. You may obtain the list from
[this form](https://jira.mongodb.org/secure/ReleaseNote.jspa?projectId=12484).

If commits from community contributors were included in this release, append the
following section:

```
**Thanks**

Thanks for our community contributors for X.Y.Z:

 * [$CONTRIBUTOR_NAME](https://github.com/$GITHUB_USERNAME)
```

Release announcements should also be posted in the [MongoDB Product & Driver Announcements: Driver Releases](https://www.mongodb.com/community/forums/tags/c/announcements/driver-releases/110/php) forum and shared on Twitter.

### Update compatibility tables in MongoDB docs

For minor releases, create a DOCSP ticket indicating whether there are changes to MongoDB Server or PHP version
compatibility. The [compatibility tables](https://docs.mongodb.com/drivers/driver-compatibility-reference#php-driver-compatibility)
in the MongoDB documentation must be updated to account for new releases. Make sure to update both MongoDB and Language
compatibility tables, as shown in [this pull request](https://github.com/mongodb/docs-ecosystem/pull/642).
