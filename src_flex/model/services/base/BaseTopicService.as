package model.services.base
{
	import flash.utils.describeType;

	import model.vo.TopicVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseTopicService extends RemoteObject
	{
	
		public const FIND:String = "TopicService.find";
		public const FIND_BY_FIELD:String = "TopicService.findByField";
		public const FIND_BY_FIELDS:String = "TopicService.findByFields";
		public const FIND_WITH_RELATED:String = "TopicService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "TopicService.findAllWithRelated";
		public const FIND_RELATED:String = "TopicService.findRelated";
		public const FIND_ALL:String = "TopicService.findAll";
		public const SAVE:String = "TopicService.save";
		public const UPDATE:String = "TopicService.update";
		public const DROP:String = "TopicService.drop";
		public const COUNT:String = "TopicService.count";
		public const COUNT_RELATED:String = "TopicService.countRelated";
		

		public function BaseTopicService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "TopicService";
		}
		
		public function find(topic_id:uint):void
		{
			this.getOperation("find").send(topic_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(topic_id:uint):void
		{
			this.getOperation("findWithRelated").send(topic_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: Category, Type: one
			//		Alias: User, Type: one
			//		Alias: posts, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(topic:TopicVO):void
		{
			this.getOperation("save").send(topic, topic.getRelatedData());
		}
		
		public function update(topic:TopicVO):void
		{
			
			var reflection:XML = describeType(topic);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = topic[property.@name];
			
			this.getOperation("update").send(topic.id, fields);
		}
		
		public function drop(topic:TopicVO):void
		{
			this.getOperation("drop").send(topic);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, topic_id:uint):void
		{
			this.getOperation("countRelated").send(field, topic_id);
		}
	}
}