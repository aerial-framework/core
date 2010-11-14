package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class PostTagVO extends AbstractVO
	{		
		public function PostTagVO()
		{
			super("org.aerial.vo.PostTag", function(field:String):*{return this[field]});
		}
		
		private var postId:*
		private var tagId:*
		private var Post:*
		private var Tag:*

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