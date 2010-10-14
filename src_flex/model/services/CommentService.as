package model.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import model.vo.CommentVO
	import com.forum.config.Config;

	public class CommentService extends AbstractService
	{
		public function CommentService()
		{
			super("CommentService", IConfig(new Config()).AMF_GATEWAY, CommentVO);
		}
	}
}