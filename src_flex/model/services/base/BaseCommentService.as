package model.services.base
{
	import flash.utils.describeType;

	import model.vo.CommentVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseCommentService extends RemoteObject
	{
	
		public const FIND:String = "CommentService.find";
		public const FIND_BY_FIELD:String = "CommentService.findByField";
		public const FIND_BY_FIELDS:String = "CommentService.findByFields";
		public const FIND_WITH_RELATED:String = "CommentService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "CommentService.findAllWithRelated";
		public const FIND_RELATED:String = "CommentService.findRelated";
		public const FIND_ALL:String = "CommentService.findAll";
		public const SAVE:String = "CommentService.save";
		public const UPDATE:String = "CommentService.update";
		public const DROP:String = "CommentService.drop";
		public const COUNT:String = "CommentService.count";
		public const COUNT_RELATED:String = "CommentService.countRelated";
		

		public function BaseCommentService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "CommentService";
		}
		
		public function find(comment_id:uint):void
		{
			this.getOperation("find").send(comment_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(comment_id:uint):void
		{
			this.getOperation("findWithRelated").send(comment_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Post, Type: one
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(comment:CommentVO):void
		{
			this.getOperation("save").send(comment);
		}
		
		public function update(comment:CommentVO):void
		{
			
			var reflection:XML = describeType(comment);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = comment[property.@name];
			
			this.getOperation("update").send(comment.id, fields);
		}
		
		public function drop(comment:CommentVO):void
		{
			this.getOperation("drop").send(comment);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, comment_id:uint):void
		{
			this.getOperation("countRelated").send(field, comment_id);
		}
	}
}