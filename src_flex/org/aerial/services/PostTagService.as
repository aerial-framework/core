package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.PostTagVO;
	import org.aerial.bootstrap.Aerial;

	public class PostTagService extends AbstractService
	{
		public function PostTagService()
		{
			super("PostTagService", Aerial.SERVER_URL, PostTagVO);
		}
	}
}