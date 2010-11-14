package model.services.base
{
	import flash.utils.describeType;

	import model.vo.TopicTagVO;
	import AMFPHP_GATEWAY_URL;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseTopicTagService extends RemoteObject
	{
	
		public const FIND:String = "TopicTagService.find";
		public const FIND_BY_FIELD:String = "TopicTagService.findByField";
		public const FIND_BY_FIELDS:String = "TopicTagService.findByFields";
		public const FIND_WITH_RELATED:String = "TopicTagService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "TopicTagService.findAllWithRelated";
		public const FIND_RELATED:String = "TopicTagService.findRelated";
		public const FIND_ALL:String = "TopicTagService.findAll";
		public const SAVE:String = "TopicTagService.save";
		public const UPDATE:String = "TopicTagService.update";
		public const DROP:String = "TopicTagService.drop";
		public const COUNT:String = "TopicTagService.count";
		public const COUNT_RELATED:String = "TopicTagService.countRelated";
		

		public function BaseTopicTagService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ;
			this.source = "TopicTagService";
		}
		
		public function find(topictag_id:uint):void
		{
			this.getOperation("find").send(topictag_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(topictag_id:uint):void
		{
			this.getOperation("findWithRelated").send(topictag_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: Topic, Type: one
			//		Alias: Tag, Type: one
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(topictag:TopicTagVO):void
		{
			this.getOperation("save").send(topictag);
		}
		
		public function update(topictag:TopicTagVO):void
		{
			
			var reflection:XML = describeType(topictag);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = topictag[property.@name];
			
			this.getOperation("update").send(topictag.id, fields);
		}
		
		public function drop(topictag:TopicTagVO):void
		{
			this.getOperation("drop").send(topictag);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, topictag_id:uint):void
		{
			this.getOperation("countRelated").send(field, topictag_id);
		}
	}
}