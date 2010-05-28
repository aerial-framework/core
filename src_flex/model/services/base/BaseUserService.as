package model.services.base
{
	import flash.utils.describeType;

	import model.vo.UserVO;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseUserService extends RemoteObject
	{
		public function BaseUserService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ServerConfig.getChannel(destination).url;
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
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: posts, Type: many
			//		Alias: comments, Type: many
			//		Alias: categories, Type: many
			
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
			var props:XMLList = describeType(user)..variable;
			
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