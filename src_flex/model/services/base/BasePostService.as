package model.services.base
{
	import flash.utils.describeType;

	import model.vo.PostVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BasePostService extends RemoteObject
	{
	
		public const GET_POST:String = "PostService.getPost";
		public const GET_POST_BY_FIELD:String = "PostService.getPostByField";
		public const GET_POST_BY_FIELDS:String = "PostService.getPostByFields";
		public const GET_POST_WITH_RELATED:String = "PostService.getPostWithRelated";
		public const GET_ALL_POST_WITH_RELATED:String = "PostService.getAllPostWithRelated";
		public const GET_RELATED:String = "PostService.getRelated";
		public const GET_ALL_POSTS:String = "PostService.getAllPosts";
		public const SAVE_POST:String = "PostService.savePost";
		public const UPDATE_POST:String = "PostService.updatePost";
		public const DELETE_POST:String = "PostService.deletePost";
		public const COUNT_POSTS:String = "PostService.countPosts";
		public const COUNT_RELATED:String = "PostService.countRelated";
		

		public function BasePostService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
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
		
		public function getAllPostWithRelated(criteria:Object=null):void
		{
			this.getOperation("getAllPostWithRelated").send(criteria);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Topic, Type: one
			//		Alias: comments, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllPosts(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllPosts").send(paged, limit, offset);
		}
								   
		public function savePost(post:PostVO):void
		{
			this.getOperation("savePost").send(post, post.getRelatedData());
		}
		
		public function updatePost(post:PostVO):void
		{
			
			var reflection:XML = describeType(post);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = post[property.@name];
			
			this.getOperation("updatePost").send(post.id, fields);
		}
		
		public function deletePost(post:PostVO):void
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