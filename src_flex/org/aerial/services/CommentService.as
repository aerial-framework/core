package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.CommentVO;
	import org.aerial.bootstrap.Aerial;

	public class CommentService extends AbstractService
	{
		public function CommentService()
		{
			super("CommentService", Aerial.SERVER_URL, CommentVO);
		}
	}
}