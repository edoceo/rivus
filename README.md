# Saltfan

PHP, libsodium, ActivityPub

## ActivityPub

* https://www.w3.org/TR/activitypub/


## libsodium



## SSL Certificates

I think you should run the sites only on port 443, leave port 80 off.
Then you can use

```
certbot certonly \
	--webroot \
	--webroot-path /opt/salt.fan/webroot \
	-d salt.fan \
	-d alpha.salt.fan \
	-d beta.salt.fan \
	-d gamma.salt.fan \
	-d delta.salt.fan \
	-d epsilon.salt.fan \
	-d zeta.salt.fan \
	-d eta.salt.fan \
	-d theta.salt.fan \
	-d iota.salt.fan \
	-d kappa.salt.fan \
	-d lambda.salt.fan \
	-d mu.salt.fan \
	-d nu.salt.fan \
	-d xi.salt.fan \
	-d omicron.salt.fan \
	-d pi.salt.fan \
	-d rho.salt.fan \
	-d sigma.salt.fan \
	-d tau.salt.fan \
	-d upsilon.salt.fan \
	-d phi.salt.fan \
	-d chi.salt.fan \
	-d psi.salt.fan \
	-d omega.salt.fan
```
