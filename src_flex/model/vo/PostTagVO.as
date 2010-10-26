package model.vo
{
	import org.aerial.rpc.AbstractVO;
	import model.vo.*;
	import mx.collections.ArrayCollection;

	[Bindable]	
	public class PostTagVO extends AbstractVO
	{		
		public function PostTagVO()
		{
			super("model.vo.PostTag", function(field:String):*{return this[field]});
		}
		
		private var _postId:*
		private var _tagId:*

		//Relations
		private var _Post:*
		private var _Tag:*

			
		public function get postId():int
		{
			return _postId;
		}
		
		public function set postId(value:int):void
		{
			_postId = value;
		}
			
		public function get tagId():int
		{
			return _tagId;
		}
		
		public function set tagId(value:int):void
		{
			_tagId = value;
		}

		//Relations
			
		public function get Post():PostVO
		{
			return _Post;
		}
		
		public function set Post(value:PostVO):void
		{
			_Post = value;
		}
			
		public function get Tag():TagVO
		{
			return _Tag;
		}
		
		public function set Tag(value:TagVO):void
		{
			_Tag = value;
		}
		
	}
}