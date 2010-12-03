package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.PostVO;
	import org.aerial.bootstrap.Aerial;

	public class PostService extends AbstractService
	{
		public function PostService()
		{
			super("PostService", Aerial.SERVER_URL, PostVO);
		}
	}
}