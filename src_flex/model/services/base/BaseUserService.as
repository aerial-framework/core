package model.services.base
{
	import flash.utils.describeType;

	import model.vo.UserVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseUserService extends RemoteObject
	{
	
		public const GET_USER:String = "UserService.getUser";
		public const GET_USER_BY_FIELD:String = "UserService.getUserByField";
		public const GET_USER_BY_FIELDS:String = "UserService.getUserByFields";
		public const GET_USER_WITH_RELATED:String = "UserService.getUserWithRelated";
		public const GET_ALL_USER_WITH_RELATED:String = "UserService.getAllUserWithRelated";
		public const GET_RELATED:String = "UserService.getRelated";
		public const GET_ALL_USERS:String = "UserService.getAllUsers";
		public const SAVE_USER:String = "UserService.saveUser";
		public const UPDATE_USER:String = "UserService.updateUser";
		public const DELETE_USER:String = "UserService.deleteUser";
		public const COUNT_USERS:String = "UserService.countUsers";
		public const COUNT_RELATED:String = "UserService.countRelated";
		

		public function BaseUserService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "UserService";
		}
		
		public function getUser(user_id:uint):void
		{
			this.getOperation("getUser").send(user_id);
		}
		
		public function getUserByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getUserByField").send(field, value, paged, limit, offset);
		}
		
		public function getUserByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getUserByFields").send(fields, values, paged, limit, offset);
		}
		
		public function getUserWithRelated(user_id:uint):void
		{
			this.getOperation("getUserWithRelated").send(user_id);
		}
		
		public function getAllUserWithRelated(criteria:Object=null):void
		{
			this.getOperation("getAllUserWithRelated").send(criteria);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: posts, Type: many
			//		Alias: comments, Type: many
			//		Alias: categories, Type: many
			//		Alias: topics, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllUsers(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllUsers").send(paged, limit, offset);
		}
								   
		public function saveUser(user:UserVO):void
		{
			this.getOperation("saveUser").send(user, user.getRelatedData());
		}
		
		public function updateUser(user:UserVO):void
		{
			
			var reflection:XML = describeType(user);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = user[property.@name];
			
			this.getOperation("updateUser").send(user.id, fields);
		}
		
		public function deleteUser(user:UserVO):void
		{
			this.getOperation("deleteUser").send(user);
		}
		
		public function countUsers():void
		{
			this.getOperation("countUsers").send();
		}
		
		public function countRelated(field:String, user_id:uint):void
		{
			this.getOperation("countRelated").send(field, user_id);
		}
	}
}