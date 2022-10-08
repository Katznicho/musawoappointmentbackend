

# ADFA APIS DOCUMENTATION

1.  Register Api(post)
    lname, fname, username, dob,password, c_password
    This register API also generates an otp and sends it to the request user via an email.
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/register


2.  Login Api(post)
    username and password only.
    This returns the details of the logged in user, with a success message
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/login


3.  Otp Verification API(post)
    otp only
    This checks if the request otp and returns the user details with that particular otp and the otp is deleted, i.e set to null, else it will ask the user to resend the email via the resend api
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/confirm
4. Resend OTP API (post)  
     username only
    The fresh Otp will be sent to the request user with using the request username and the otp of this particular user will be updated,

                API URL(post)
                https://dev.ssentezo.com/appointmentBackend/public/index.php/api/resend



4.  Update Health workers's Address API
    address, longitude, latitude
    This updates the health Worker's current address.

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/update-doctor/{id}
              note. the id is for the health worker

5.  Update Client Address API
    address, phone, health_worker needed in terms of either {Doctor, Nurse, physiologist, Technician}, longitude, latitude
    This updates the client's current address .

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/update-client/{id}
              note. the id is for the Client

6.  Getting the closest Heath worker Address API
    This requires the client(user) to have already updated his table by filling in the current address, longitude , latitude and the health worker needed.
    This returns only one health worker in the category of the requested and who is closest to the client, and the details of the request.

                  API URL(get)
                  https://dev.ssentezo.com/appointmentBackend/public/index.php/api/getDoctor/{id}
                  Note: the id here is for the client making the request
                  This also sends a mail to the doctor, to check the request

                  returns: if not found
                  {
                    "message": "No doctor Available"

                  }

                  else it will return
                  {
    "response": "success",
    "data": {
        "doctor": {
            "id": 1,
            "name": "Ssekyanzi Ronald",
            "email": "cnakyanzi2019@gmail.com",
            "phone": null,
            "role": "Doctor",
            "address": "127.0.0.1",
            "isDoctor": 1,
            "status": "active",
            "latitude": null,
            "longitude": null,
            "created_at": "2021-08-17T21:44:53.000000Z",
            "updated_at": "2021-08-17T21:44:53.000000Z",
            "distance": null
        },
        "request": {
            "id": 19,
            "client_id": "1",
            "doctor_id": "1",
            "message": "You have a new request from cathy nakya Located in kampala",
            "status": "pending",
            "created_at": null,
            "updated_at": null
        }
    }
}

7.  Accept Request
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/acceptRequest/{id}
    Note: the id here is for that particular Request

        {
        "message": "Request accepted",
        "data": {
            "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "You have a new request from cathy nakya Located in kampala",
                "status": "accepted",
            }
        }

    }

8.  cancel Request by the doctor
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/cancelRequest/{id}
    Note: the id here is for that particular Request that needs to be cancelled
    returns:
    {
    "message": "Request has been cancelled"
    }

9.  Get the doctors current Request
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/getRequests/{id}
    Note: This Id is for the doctor
    returns, the latest client to make a request to this particular Doctor  and the request details
    {
    "message": "Doctor Requests Returned successfully",
    "data": { [
                "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "You have a new request from cathy nakya Located in kampala",
                "status": "accepted",
            }],
    "client": [
    {
    "id": 2,
    "fname": "catalina",
    "lname": "Nakya",
    "phone": "0756208509",
    "address": "kampala",
    "email": "cathie@gmail.com",
    "isDoctor": 0,
    "dob": "2000/4/09",
    "latitude": "0.05762779999999999",
    "longitude": "32.4621955",
    "health_worker": "Doctor",
    "created_at": "2021-08-20 07:59:51",
    "updated_at": "2021-08-20 08:00:57"
    }
    ]
    }
    }


    10.  complete Request by the Doctor
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/completeRequest/{id}
    Note: the id here is for that particular Request that needs to be completed
    it also turns the requested health worker back to active
    returns:
    {
    "message": "Request Completed"
    }

    11.  cancel Request by the client
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/cancelClient/{id}
    Note: the id here is for that particular Request that needs to be cancelled
    it also turns the requested health worker back to active
    returns:
        {
    "message": " Request cancelled successfully",


12.  Get the doctor's History
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/doctorHistory/{id}
    Note: This Id is for the doctor
    returns, the all the clients and requests that made a request to this particular Doctor  and the request details
    {
    "message": "Doctor Requests Returned successfully",
    "data": { [
                "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "You have a new request from cathy nakya Located in kampala",
                "status": "accepted",
            }],
    "client": [
    {
    "id": 2,
    "fname": "catalina",
    "lname": "Nakya",
    "phone": "0756208509",
    "address": "kampala",
    "email": "cathie@gmail.com",
    "isDoctor": 0,
    "dob": "2000/4/09",
    "latitude": "0.05762779999999999",
    "longitude": "32.4621955",
    "health_worker": "Doctor",
    "created_at": "2021-08-20 07:59:51",
    "updated_at": "2021-08-20 08:00:57"
    }
    ]
    }
    }

13.  Get the client history
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/clientHistory/{id}
    Note: This Id is for the client
    returns, the all client's request details
    {
    "message": "Doctor Requests Returned successfully",
    "data": { [
                "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "You have a new request from cathy nakya Located in kampala",
                "status": "accepted",
            }],
    "doctor": [
              [
                {
                    "id": 3,
                    "name": "Nakyanzi Catherine",
                    "email": "cathynakya1@gmail.com",
                    "phone": null,
                    "role": "Doctor",
                    "address": "127.0.0.1",
                    "password": "$2y$10$nF/OXOkirxuW4VWwrNxaFuRzS52OdYqp26ImlJN2Ot9qG9gIiEWH6",
                    "isDoctor": 1,
                    "status": "active",
                    "latitude": null,
                    "longitude": null,
                    "created_at": "2021-08-20 10:18:20",
                    "updated_at": "2021-08-20 10:18:20"
                }
            ],
    ]
    }
    }

    14.  Get the Client's current Request
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/currentRequest/{id}
    Note: This Id is for the client
    returns, the latest client,s request and the doctor .
    {
    "message": "Doctor Requests Returned successfully",
    "data": { [
                "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "You have a new request from cathy nakya Located in kampala",
                "status": "accepted",
            }],
             "doctor": [
                [
                  {
                    "id": 3,
                    "name": "Nakyanzi Catherine",
                    "email": "cathynakya1@gmail.com",
                    "phone": null,
                    "role": "Doctor",
                    "address": "127.0.0.1",
                    "password": "$2y$10$nF/OXOkirxuW4VWwrNxaFuRzS52OdYqp26ImlJN2Ot9qG9gIiEWH6",
                    "isDoctor": 1,
                    "status": "active",
                    "latitude": null,
                    "longitude": null,
                    "created_at": "2021-08-20 10:18:20",
                    "updated_at": "2021-08-20 10:18:20"
                }
            ],
    ]
    }
    }

    15. Confirm request completed Request by the Client
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/completeClient/{id}
    Note: the id here is for that particular Request that has been completed and needs to be confirmed
    parameters required; (client_review, rating)
    returns:
        {
    "message": " Request completed successfully",
    "data": { [
                "request": {
                "id": 1,
                "client_id": "1",
                "doctor_id": "1",
                "message": "",
                "status": ""
                "client_review"
            }],

16.  Activate Health workers's Status API
    This updates the health Worker's status to active.

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/activate-doctor/{id}
              note. the id is for the health worker

              {
                "response": "success",
                 "message": "Doctor Status activated successfully"
               }


17.  Deactivate Health workers's Status API
    This updates the health Worker's status to inactive.

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/deactivate-doctor/{id}
              note. the id is for the health worker

              {
                "response": "success",
                "message": "Doctor Status deactivated successfully"
               }

18.  get Health workers's Status API
    This updates the health Worker's status to inactive.

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/doctor-status/{id}
              note. the id is for the health worker

        {
            "message": "Doctor Status retrieved successfully",
            "data": {
                "status": "active"
            }
        }

19.  Forgot Password Api
    The only required Parameter is the username

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/forgotPassword
              This sends an Otp to the client via the username entered

           {
                "message": "Verification Code has been to your Email"
            }

20.  Verify Otp
    The only required Parameter is the otp

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/verifyOtp
{
    "message": "Logged successfully",
    "data": {
        "email": "cathynakya1@gmail.com"
    }
}


21.  Reset Password
    The only required Parameters are password and c_password

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/resetPassword/{email}
              This is the {email} is the username that was returned after otp verification

           {
                "message": "Password Reset Successfully"
            }

21. Get all Laboratory services
              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/allServices
              This returns all the laboratory services offered

{
    "response": "success",
    "data": [
        {
            "id": 2,
            "name": "teeth cleaning",
            "price": "50000",
            "created_at": "2021-09-04T15:24:28.000000Z",
            "updated_at": "2021-09-08T15:07:58.000000Z"
        },
        {
            "id": 3,
            "name": "Blood testing",
            "price": "566",
            "created_at": "2021-09-08T15:07:34.000000Z",
            "updated_at": "2021-09-08T15:07:34.000000Z"
        }
    ]
}

22. Request for a Service
    service_name, client_contact, client_address 

              API URL(post)
              https://dev.ssentezo.com/appointmentBackend/public/index.php/api/requestService/{id}
              note. the id is for the Client making the lab service request

              {
    "response": "success",
    "data": {
        "request": {
            "id": 1,
            "client_id": "1",
            "service_name": "Cleaning Teeth",
            "client_name": "cathy",
            "client_address": "namugongo",
            "client_contact": "0756333333",
            "status": "pending",
            "price": "44000",
            "created_at": "2021-09-04 15:21:23",
            "updated_at": "2021-09-04 15:21:23"
        }
    }
}

23. Confirm Lab request service completed  by the Client
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/rateLabRequest/{id}
    Note: the id here is for that particular Request that has been completed and needs to be confirmed
    parameters required; (client_review, rating)
    returns:
{
    "message": "Request confirmed by client Completed",
    "data": {
        "request": {
            "id": 1,
            "client_id": "1",
            "service_name": "washing",
            "client_name": "cathy",
            "client_address": "namugongo",
            "client_contact": "0756333333",
            "status": "completed",
            "price": "3002",
            "client_review": "hello, good",
            "rating": "5",
            "created_at": "2021-09-15 06:46:03",
            "updated_at": "2021-09-15 06:52:01"
        }
    }
}


24.  cancel Lab Request by the Client
    API URL
    https://dev.ssentezo.com/appointmentBackend/public/index.php/api/cancelLabRequest/{id}
    Note: the id here is for that particular Request that needs to be cancelled
    returns:
    {
    "message": "Lab Request has been cancelled"
    }


25. Client On going Current Lab Request
      Api URL
      https://dev.ssentezo.com/appointmentBackend/public/index.php/api/currentLabRequest/{id}
       Note. id is for the client

       returns:
       {
    "message": "Client Lab Request Returned successfully",
    "data": {
        "request": {
            "id": 2,
            "client_id": "1",
            "service_name": "sweeping",
            "client_name": "cathy",
            "client_address": "namugongo",
            "client_contact": "0756333333",
            "status": "pending",
            "price": "2000",
            "client_review": null,
            "rating": "0",
            "created_at": "2021-09-15 07:55:22",
            "updated_at": "2021-09-15 07:56:15"
        }
    }
}

26. Update Push Token API
      Api URL
      https://dev.ssentezo.com/appointmentBackend/public/index.php/updateToken/{id}
       Note. id is for the user

       returns:
       {
         "message": "Token Updated Successfully"
    }


27. Retrieve Push Token API
      Api URL
      https://dev.ssentezo.com/appointmentBackend/public/index.php/RetrieveToken/{id}
       Note. id is for the user

{
    "message": "Token Updated Successfully",
    "data": {
        "push_token": [
            {
                "push_token": "My Name is Cathy"
            }
        ]
    }
}
