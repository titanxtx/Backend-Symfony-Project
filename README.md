# Backend-Symfony-Project

### Get Request
#### Get user information
###### Parameters
 <pre>only parameter applied only to type=user -  pagination is for type=users only right now</pre>\
 <pre>
 <b>type=</b> user or email or phone or social
 <b>type_id=</b> Any id of type's table  Many can be selected at once like type_id=1,2,3,4,5 or just type_id=1
 <b>only=</b> select columns to show only. Can be more than one. user_id or name or active_status or emails, email_id, email_address,phone_numbers,social_media,updated_date,created_date
 <b>page=</b> Any number greater than or equal to 1
 <b>amount=</b> Any amount between or on 1-200
 <b>name=</b> Any names of a user. More than one can be used
 <b>sortby=</b> Sort by a column - user_id, name, created_date, updated_date, email_amt, phone_amt, or social_amt
 <b>order=</b> asc or desc
 </pre>
 
 * Get a user\
 http://localhost:8080/?type=user&type_id=2
 
 * Get all users information\
 http://localhost:8080/
 
 * Get only certain user information \
 http://localhost:8080/?type=user&type_id=2&only=name,user_id,emails
 
 * Get multiple users information but only certain things from them \
 http://localhost:8080/?type=user&type_id=1,2,3,4&only=name,user_id,emails
 
 * Get multiple users information but only certain things from them   -- with pagination--\
 http://localhost:8080/?type=user&type_id=1,2,3,4&only=name,user_id,emails&page=1&amount=2
 
 * Get a email\
 http://localhost:8080/?type=email&type_id=2
 
 * Get many emails\
 http://localhost:8080/?type=email&type_id=1,2,3
 
 * Get a phone number\
 http://localhost:8080/?type=phone&type_id=2
 
 * Get many phone numbers\
 http://localhost:8080/?type=phone&type_id=1,2,3
 
 * Get a socialmedia with their id\
 http://localhost:8080/?type=social&type_id=2
 
 * Get many socialmedias with their id\
 http://localhost:8080/?type=social&type_id=1,2,3`
 
 ### Post Request
 #### Insert user information
 ###### Parameters
 <pre>
 <b>type=</b> user or email or phone or social
 <b>user_id=</b> Many can be selected at once like user_id=1,2,3,4,5 or just user_id=1
 <b>active_status=</b> 0 or 1
 <b>email=</b> Any email address - Used when adding a user or email
 <b>phone=</b> Any phone number - Used when adding a user or phone number
 <b>name=</b> Any names of a user. More than one can be used
 <b>social_type=</b> Name of social media outlet like Facebook, Instagram, or Twitter
 <b>social_link=</b> Social media link to the profile
 </pre>
 * New user\
   http://localhost:8080/?type=user&email=kevin3@123.321&name=Kevin3&phone=322-432-5433&social_type=Twitter&social_link=http://www.twitter.com/test103
  
 * New email\
   http://localhost:8080/?type=email&email=testing1@test.test&user_id=2
 
 * New phone\
   http://localhost:8080/?type=phone&phone=932-323-5432&user_id=2
  
 * New social\
   http://localhost:8080/?type=social&user_id=2&social_type=instagram&social_link=http://www.instagram.com/testz
   
### Put Request
#### Update information
 ###### Parameters
<pre>
 <b>type=</b> user or email or phone or social
 <b>type_id=</b> Id of type. Many can be selected at once like type_id=1,2,3,4,5 or just type_id=1
 <b>active_status=</b> 0 or 1
 <b>email=</b> Any email address - Used when adding a user or email
 <b>phone=</b> Any phone number - Used when adding a user or phone number
 <b>name=</b> Any names of a user. More than one can be used
 <b>social_type=</b> Name of social media outlet like Facebook, Instagram, or Twitter
 <b>social_link=</b> Social media link to the profile
</pre>
* update user by user id\
  http://localhost:8080/?type=user&type_id=1&name=Frank      ---changing name
  http://localhost:8080/?type=user&type_id=1&active_status=1      ---changing active_status

 * update email by email id\
  http://localhost:8080/?type=email&type_id=1&email=test@test.test
 * update phone by phone number id\
  http://localhost:8080/?type=phone&type_id=1&phone=231-432-5431
 
 * update social by social media info by id
  http://localhost:8080/?type=social&type_id=1&social_type=facebook&social_link=http://www.facebook.com/test123321
  
  
 ### Delete Request
#### Delete information
 ###### Parameters
 <pre>
 <b>type=</b> user or email or phone or social
 <b>type_id=</b> Id of type
 </pre>
 * delete user by user id\
  http://localhost:8080/?type=user&type_id=1
 * delete email by email id\
 http://localhost:8080/?type=email&type_id=1
 * delete phone number by phone number id\
  http://localhost:8080/?type=phone&type_id=1
 * delete social media by social media id\
 http://localhost:8080/?type=social&type_id=1
