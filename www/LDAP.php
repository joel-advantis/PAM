  if ((!$userValidated) && ($USE_LDAP)) {
         // Before giving up, let's use LDAP to validate the user
         // open a connection to LDAP to confirm
         $ldapbind = 0;
         $ldapconn = ldap_connect("$LDAP_SERVER")
                  or die("Could not connect to LDAP server.");

         if ($ldapconn) {
               // binding to ldap server
              # $userName = str_replace ("AD\\\\", "", $userName);
              # $ldapbind = ldap_bind($ldapconn, "AD\\$userName", "$password");
               $ldapbind = ldap_bind($ldapconn, "$userName", "$password");
         }
         // verify user name
         if ($ldapbind) {
               // Successful Connection
               $userValidated = 1;
       }
 }
