package model.services.base
{
	import flash.utils.describeType;

	import model.vo.PostVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BasePostService extends RemoteObject
	{
	
		public const FIND:String = "PostService.find";
		public const FIND_BY_FIELD:String = "PostService.findByField";
		public const FIND_BY_FIELDS:String = "PostService.findByFields";
		public const FIND_WITH_RELATED:String = "PostService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "PostService.findAllWithRelated";
		public const FIND_RELATED:String = "PostService.findRelated";
		public const FIND_ALL:String = "PostService.findAll";
		public const SAVE:String = "PostService.save";
		public const UPDATE:String = "PostService.update";
		public const DROP:String = "PostService.drop";
		public const COUNT:String = "PostService.count";
		public const COUNT_RELATED:String = "PostService.countRelated";
		

		public function BasePostService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "PostService";
		}
		
		public function find(post_id:uint):void
		{
			this.getOperation("find").send(post_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(post_id:uint):void
		{
			this.getOperation("findWithRelated").send(post_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Topic, Type: one
			//		Alias: comments, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(post:PostVO):void
		{
			this.getOperation("save").send(post, post.getRelatedData());
		}
		
		public function update(post:PostVO):void
		{
			
			var reflection:XML = describeType(post);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = post[property.@name];
			
			this.getOperation("update").send(post.id, fields);
		}
		
		public function drop(post:PostVO):void
		{
			this.getOperation("drop").send(post);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, post_id:uint):void
		{
			this.getOperation("countRelated").send(field, post_id);
		}
	}
}