package model.services.base
{
	import flash.utils.describeType;

	import models.Comment;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseCommentService extends RemoteObject
	{
		public function BaseCommentService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ServerConfig.getChannel(destination).url;
			this.source = "CommentService";
		}
		
		public function getComment(comment_id:uint):void
		{
			this.getOperation("getComment").send(comment_id);
		}
		
		public function getCommentByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getCommentByField").send(field, value, paged, limit, offset);
		}
		
		public function getCommentByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getCommentByFields").send(fields, values, paged, limit, offset);
		}
		
		public function getCommentWithRelated(comment_id:uint):void
		{
			this.getOperation("getCommentWithRelated").send(comment_id);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: User, Type: one
			//		Alias: Post, Type: one
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllComments(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllComments").send(paged, limit, offset);
		}
								   
		public function saveComment(comment:Comment):void
		{
			this.getOperation("saveComment").send(comment, comment.getRelatedData());
		}
		
		public function updateComment(comment:Comment):void
		{
			var props:XMLList = describeType(comment)..variable;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = comment[property.@name];
			
			this.getOperation("updateComment").send(comment.id, fields);
		}
		
		public function deleteComment(comment:Comment):void
		{
			this.getOperation("deleteComment").send(comment);
		}
		
		public function countComments():void
		{
			this.getOperation("countComments").send();
		}
		
		public function countRelated(field:String, comment_id:uint):void
		{
			this.getOperation("countRelated").send(field, comment_id);
		}
	}
}