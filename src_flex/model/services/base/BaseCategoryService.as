package model.services.base
{
	import flash.utils.describeType;

	import models.Category;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseCategoryService extends RemoteObject
	{
		public function BaseCategoryService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ServerConfig.getChannel(destination).url;
			this.source = "CategoryService";
		}
		
		public function getCategory(category_id:uint):void
		{
			this.getOperation("getCategory").send(category_id);
		}
		
		public function getCategoryByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getCategoryByField").send(field, value, paged, limit, offset);
		}
		
		public function getCategoryByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getCategoryByFields").send(fields, values, paged, limit, offset);
		}
		
		public function getCategoryWithRelated(category_id:uint):void
		{
			this.getOperation("getCategoryWithRelated").send(category_id);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: posts, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllCategories(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllCategories").send(paged, limit, offset);
		}
								   
		public function saveCategory(category:Category):void
		{
			this.getOperation("saveCategory").send(category, category.getRelatedData());
		}
		
		public function updateCategory(category:Category):void
		{
			var props:XMLList = describeType(category)..variable;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = category[property.@name];
			
			this.getOperation("updateCategory").send(category.id, fields);
		}
		
		public function deleteCategory(category:Category):void
		{
			this.getOperation("deleteCategory").send(category);
		}
		
		public function countCategories():void
		{
			this.getOperation("countCategories").send();
		}
		
		public function countRelated(field:String, category_id:uint):void
		{
			this.getOperation("countRelated").send(field, category_id);
		}
	}
}