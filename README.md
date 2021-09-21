# Saltfan

PHP, libsodium, ActivityPub

## ActivityPub

* https://www.w3.org/TR/activitypub/
* https://www.w3.org/TR/activitystreams-vocabulary/
* https://socialhub.activitypub.rocks/t/guide-for-new-activitypub-implementers/479


## libsodium

* https://nacl.cr.yp.to/
* https://doc.libsodium.org/

## Creating a Site

To get the Saltfan code to host a site you'll need to create the directory in `./var`.
We intentionally don't make the path automatically because then the system get's littered with new paths from all kinds of drive-by HTTP noise.
Better to serve them a `501` or `404` type resonse.
