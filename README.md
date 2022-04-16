# Laravel Passport Authentication With Oauth2  
## Grant Type - Password  

#### In my authentication, the client does not need to add an access token to the headers every time, it does not need to refresh the access_token when it expires, All logic is contained on the server side and rightly so, storing tokens on the client side is very insecure and wrong.  

## Middlewares:  
#### CookieSetter - This middleware gets markers from the cookies and add to headers on each request.    
#### IsValidToken - This middleware will check if the cookies contain a refresh token but no access token, it will update the tokens using the refresh token which it will take from the cookie, thus after a successful update it will automatically add new tokens to the request headers, if the refresh token is not valid it will throw exceptions and the client will need to re-authorize



