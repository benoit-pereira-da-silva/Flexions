//
//  LoginUser.swift
//  Bartleby
//
//  Generated by Flexions for benoit@pereira-da-silva.com
//  https://github.com/benoit-pereira-da-silva/Flexions
//
// DO NOT MODIFY THIS FILE YOUR MODIFICATIONS WOULD BE ERASED ON NEXT GENERATION!
// IF NECESSARY YOU CAN MARK THIS FILE TO BE PRESERVED
// IN THE PREPROCESSOR BY ADDING IN Hypotypose::instance().preservePath
//

import Foundation
import Alamofire
import ObjectMapper

@objc(LoginUserParameters) class LoginUserParameters : JObject {

    let NO_EMAIL="NO_EMAIL"
    let UNDEFINED_PASSWORD="UNDEFINED_PASSWORD"

    // The email is used as a primary key to identify the user
    var email:String
    // The password
    var password:String

    required init(){
        self.email=NO_EMAIL
        self.password=UNDEFINED_PASSWORD
        super.init()
    }

    required init(email:String,password:String){
        self.email=email
        self.password=password
        super.init()
    }

    // MARK: Mappable

    required init?(_ map: Map) {
        self.email=NO_EMAIL
        self.password=UNDEFINED_PASSWORD
        super.init(map)
        mapping(map)
    }


    override func mapping(map: Map) {
        super.mapping(map)
        email <- map["email"]
        password <- map["password"]

    }
}
@objc(LoginUser) class LoginUser : JObject{

    static func execute(dID:String,
        parameters:LoginUserParameters,
        sucessHandler success:()->(),
        failureHandler failure:(context:JHTTPResponse)->()){

            if parameters.email==parameters.NO_EMAIL ||
                parameters.password==parameters.UNDEFINED_PASSWORD{
                    var reactions = Array<Bartleby.Reaction> ()
                    let context = JHTTPResponse( code: 0,
                        caller: "LoginUser.execute",
                        relatedURL:nil,
                        httpStatusCode: 0,
                        response: nil )

                    let m = NSLocalizedString("Authentication email or password is not defined",
                        comment: "Authentication login failure description")
                    let failureReaction =  Bartleby.Reaction.DispatchAdaptiveMessage(
                        context: context,
                        title: NSLocalizedString("Unsuccessfull attempt result.isFailure is true",
                            comment: "Unsuccessfull attempt"),
                        body:m ,
                        trigger:{ (selectedIndex) -> () in
                            Bartleby.bprint("Post presentation message selectedIndex:\(selectedIndex)")
                    })
                    reactions.append(failureReaction)
                    Bartleby.sharedInstance.perform(reactions, forContext: context)
                    failure(context:context)
                    return
            }

            let pathURL=Configuration.BASE_URL.URLByAppendingPathComponent("/user/login")
            let dictionary:Dictionary<String, AnyObject>?=Mapper().toJSON(parameters)
            let urlRequest=HTTPManager.mutableRequestWithToken(documentID:dID,withActionName:"LoginUser" ,forMethod:Method.POST, and: pathURL)
            let r:Request=request(ParameterEncoding.JSON.encode(urlRequest, parameters: dictionary).0)
            r.responseString{ response in

                let request=response.request
                let result=response.result
                let response=response.response

                // Bartleby consignation

                let context = JHTTPResponse( code: 100,
                    caller: "LoginUser.execute",
                    relatedURL:request?.URL,
                    httpStatusCode: response?.statusCode ?? 0,
                    response: response )

                // React according to the situation
                var reactions = Array<Bartleby.Reaction> ()
                reactions.append(Bartleby.Reaction.Track(result: nil, context: context)) // Tracking

                if result.isFailure {
                    let m = NSLocalizedString("authentication login",
                        comment: "authentication login failure description")
                    let failureReaction =  Bartleby.Reaction.DispatchAdaptiveMessage(
                        context: context,
                        title: NSLocalizedString("Unsuccessfull attempt result.isFailure is true",
                            comment: "Unsuccessfull attempt"),
                        body:"\(m) httpStatus code = \(response?.statusCode ?? 0 )" ,
                        trigger:{ (selectedIndex) -> () in
                            Bartleby.bprint("Post presentation message selectedIndex:\(selectedIndex)")
                    })
                    reactions.append(failureReaction)
                    failure(context:context)
                }else{
                    if let statusCode=response?.statusCode {
                        if 200...299 ~= statusCode {
                            success()
                        }else{
                            // Bartlby does not currenlty discriminate status codes 100 & 101
                            // and treats any status code >= 300 the same way
                            // because we consider that failures differentiations could be done by the caller.
                            let m = NSLocalizedString("authentication login",
                                comment: "authentication login failure description")
                            let failureReaction =  Bartleby.Reaction.DispatchAdaptiveMessage(
                                context: context,
                                title: NSLocalizedString("Unsuccessfull attempt",
                                    comment: "Unsuccessfull attempt"),
                                body:"\(m) httpStatus code = \(statusCode)" ,
                                trigger:{ (selectedIndex) -> () in
                                    Bartleby.bprint("Post presentation message selectedIndex:\(selectedIndex)")
                            })
                            reactions.append(failureReaction)
                            failure(context:context)
                        }
                    }
                }
                //Let's react according to the context.
                Bartleby.sharedInstance.perform(reactions, forContext: context)

            }
        }

}
