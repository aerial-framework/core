package model.services.base
{
	import flash.utils.describeType;

	import model.vo.UserVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseUserService extends RemoteObject
	{
	
		public const FIND:String = "UserService.find";
		public const FIND_BY_FIELD:String = "UserService.findByField";
		public const FIND_BY_FIELDS:String = "UserService.findByFields";
		public const FIND_WITH_RELATED:String = "UserService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "UserService.findAllWithRelated";
		public const FIND_RELATED:String = "UserService.findRelated";
		public const FIND_ALL:String = "UserService.findAll";
		public const SAVE:String = "UserService.save";
		public const UPDATE:String = "UserService.update";
		public const DROP:String = "UserService.drop";
		public const COUNT:String = "UserService.count";
		public const COUNT_RELATED:String = "UserService.countRelated";
		

		public function BaseUserService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "UserService";
		}
		
		public function find(user_id:uint):void
		{
			this.getOperation("find").send(user_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(user_id:uint):void
		{
			this.getOperation("findWithRelated").send(user_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: posts, Type: many
			//		Alias: comments, Type: many
			//		Alias: categories, Type: many
			//		Alias: topics, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(user:UserVO):void
		{
			this.getOperation("save").send(user, user.getRelatedData());
		}
		
		public function update(user:UserVO):void
		{
			
			var reflection:XML = describeType(user);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = user[property.@name];
			
			this.getOperation("update").send(user.id, fields);
		}
		
		public function drop(user:UserVO):void
		{
			this.getOperation("drop").send(user);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, user_id:uint):void
		{
			this.getOperation("countRelated").send(field, user_id);
		}
	}
}