package model.services.base
{
	import flash.utils.describeType;

	import model.vo.CategoryVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseCategoryService extends RemoteObject
	{
	
		public const FIND:String = "CategoryService.find";
		public const FIND_BY_FIELD:String = "CategoryService.findByField";
		public const FIND_BY_FIELDS:String = "CategoryService.findByFields";
		public const FIND_WITH_RELATED:String = "CategoryService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "CategoryService.findAllWithRelated";
		public const FIND_RELATED:String = "CategoryService.findRelated";
		public const FIND_ALL:String = "CategoryService.findAll";
		public const SAVE:String = "CategoryService.save";
		public const UPDATE:String = "CategoryService.update";
		public const DROP:String = "CategoryService.drop";
		public const COUNT:String = "CategoryService.count";
		public const COUNT_RELATED:String = "CategoryService.countRelated";
		

		public function BaseCategoryService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "CategoryService";
		}
		
		public function find(category_id:uint):void
		{
			this.getOperation("find").send(category_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(category_id:uint):void
		{
			this.getOperation("findWithRelated").send(category_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: topics, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(category:CategoryVO):void
		{
			this.getOperation("save").send(category);
		}
		
		public function update(category:CategoryVO):void
		{
			
			var reflection:XML = describeType(category);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = category[property.@name];
			
			this.getOperation("update").send(category.id, fields);
		}
		
		public function drop(category:CategoryVO):void
		{
			this.getOperation("drop").send(category);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, category_id:uint):void
		{
			this.getOperation("countRelated").send(field, category_id);
		}
	}
}