# Process:
# - open bccert.cnf.dist, change on line 'CN = <BC server>'  <BC server> to your local machine name and save the file as bccert.cnf
# - run "make generate-request"
# - upload generated certificate request bccert.csr file to CSOB CEB application, generate and download certificate from there
# - copy downloaded certificate bccert.crt file to this folder
# - run "make generate-cert"
# - you will be asked to set password, set some
# - bccert.p12 and bccert.pem files should appear in this folder
# - use bccert.pem as local_cert and tour password as passphrase while creating SoapClient in BusinessConnectorClientFactory

generate-request:
	openssl req -config bccert.cnf -new -keyout bccert.key -out bccert.csr
	chmod 400 bccert.key

generate-cert:
	openssl pkcs12 -export -in bccert.crt -inkey bccert.key -out bccert.p12
	openssl pkcs12 -in bccert.p12 -out bccert.pem -clcerts

cerificate-info:
	openssl x509 -in bccert.crt -text -noout
