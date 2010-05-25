package model.services.base
{
	import flash.utils.describeType;

	import models.Post;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BasePostService extends RemoteObject
	{
		public function BasePostService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ServerConfig.getChannel(destination).url;
			this.source = "PostService";
		}
		
		public function getPost(post_id:uint):void
		{
			this.getOperation("getPost").send(post_id);
		}
		
		public function getPostByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getPostByField").send(field, value, paged, limit, offset);
		}
		
		public function getPostByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getPostByFields").send(fields, values, paged, limit, offset);
		}
		
		public function getPostWithRelated(post_id:uint):void
		{
			this.getOperation("getPostWithRelated").send(post_id);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Category, Type: one
			//		Alias: comments, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllPosts(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllPosts").send(paged, limit, offset);
		}
								   
		public function savePost(post:Post):void
		{
			this.getOperation("savePost").send(post, post.getRelatedData());
		}
		
		public function updatePost(post:Post):void
		{
			var props:XMLList = describeType(post)..variable;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = post[property.@name];
			
			this.getOperation("updatePost").send(post.id, fields);
		}
		
		public function deletePost(post:Post):void
		{
			this.getOperation("deletePost").send(post);
		}
		
		public function countPosts():void
		{
			this.getOperation("countPosts").send();
		}
		
		public function countRelated(field:String, post_id:uint):void
		{
			this.getOperation("countRelated").send(field, post_id);
		}
	}
}