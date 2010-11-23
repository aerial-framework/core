package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CommentVO;

	public class CommentService extends AbstractService
	{
		public function CommentService()
		{
			super("CommentService", "http://aerial-test/server.php", CommentVO);
		}
	}
}