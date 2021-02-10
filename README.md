# Backend-Symfony-Project

### Get Request
#### Get user information
 only parameter applied only to type=user -  pagination is for type=users only right now\
 `type= user or email or phone or social`\
 `type_id= Any id of type's table  Many can be selected at once like type_id=1,2,3,4,5 or just type_id=1`\
 `only= select columns to show only. Can be more than one. user_id or name or active_status or emails, email_id, email_address,phone_numbers,social_media,updated_date,created_date  \`
 `page= Any number greater than or equal to 1`\
 `amount= Any amount between or on 1-200`\
 `name= Any names of a user. More than one can be used`\
 `sortby= Sort by a column - user_id, name, created_date, updated_date, email_amt, phone_amt, or social_amt`\
 `order= asc or desc`
 
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

 * delete user by user id\
  http://localhost:8080/?type=user&type_id=1
 * delete email by email id\
 http://localhost:8080/?type=email&type_id=1
 * delete phone number by phone number id\
  http://localhost:8080/?type=phone&type_id=1
 * delete social media by social media id\
 http://localhost:8080/?type=social&type_id=1
